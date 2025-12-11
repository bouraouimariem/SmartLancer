<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../model/database.php';
    require_once __DIR__ . '/../model/reponse.php';
    require_once __DIR__ . '/../model/avis.php';

    $db = (new Database())->getConnection();
    $reponseModel = new Reponse($db);
    $avisModel = new Avis($db);

    // Ensure settings and notifications tables exist (simple lightweight migrations)
    $db->exec("CREATE TABLE IF NOT EXISTS app_settings (
        name VARCHAR(100) NOT NULL PRIMARY KEY,
        value TEXT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        avis_id INT DEFAULT NULL,
        reponse_id INT DEFAULT NULL,
        to_email VARCHAR(255) DEFAULT NULL,
        subject TEXT,
        body TEXT,
        sent TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    // Helper to read an app setting
    function getAppSetting($db, $name, $default = null) {
        try {
            $stmt = $db->prepare('SELECT value FROM app_settings WHERE name = :name LIMIT 1');
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['value'])) return $row['value'];
        } catch (Exception $e) {
            // ignore
        }
        return $default;
    }

    function setAppSetting($db, $name, $value) {
        try {
            $stmt = $db->prepare('INSERT INTO app_settings (name, value) VALUES (:name, :value) ON DUPLICATE KEY UPDATE value = :value2');
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':value2', $value);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('[setAppSetting] ' . $e->getMessage());
            return false;
        }
    }

    $action = $_POST['action'] ?? '';

    $result = ['success' => false, 'message' => 'Action non spécifiée'];

// Action: Toggle notifications settings (admin only)
if ($action === 'toggle_notifications') {
    $enabled = isset($_POST['enabled']) ? (int)$_POST['enabled'] : 0;
    $enabled = $enabled ? '1' : '0';
    
    if (setAppSetting($db, 'notifications_enabled', $enabled)) {
        $result = [
            'success' => true,
            'message' => 'Paramètres des notifications mis à jour',
            'enabled' => $enabled == '1'
        ];
    } else {
        $result['message'] = 'Erreur lors de la mise à jour du paramètre.';
    }
    
    echo json_encode($result);
    exit;
}

// Action: Get current notifications settings
if ($action === 'get_notifications_settings') {
    $enabled = getAppSetting($db, 'notifications_enabled', '1');
    $result = [
        'success' => true,
        'enabled' => $enabled == '1'
    ];
    
    echo json_encode($result);
    exit;
}

// Common configuration
$MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
$ALLOWED_MIMES = [
    'image/jpeg', 
    'image/png', 
    'image/gif', 
    'application/pdf',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // DOCX
];
$ALLOWED_ROLES = ['admin', 'client', 'freelancer'];
$ALLOWED_TYPES = ['freelance', 'admin'];

function uploadPieceJointe(array $file, array $allowedMimes, int $maxSize)
{
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['error' => 'Aucun fichier téléchargé.'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux (dépassement INI).',
            UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (dépassement formulaire).',
            UPLOAD_ERR_PARTIAL => 'Fichier téléchargé partiellement.',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier sélectionné.',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
            UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire sur le disque.',
            UPLOAD_ERR_EXTENSION => 'Extension de fichier non autorisée.',
        ];
        return ['error' => $errors[$file['error']] ?? 'Erreur d\'upload inconnue.'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['error' => 'Fichier trop volumineux (max 2MB).'];
    }

    // Validate MIME using finfo (more reliable than client-sent type)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowedMimes, true)) {
        return ['error' => 'Type de fichier non autorisé. MIME détecté: ' . $mime];
    }

    $uploadsDir = __DIR__ . '/../view/uploads';
    if (!is_dir($uploadsDir)) {
        if (!mkdir($uploadsDir, 0755, true)) {
            return ['error' => 'Impossible de créer le dossier d\'upload.'];
        }
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safe = bin2hex(random_bytes(8)) . ($ext ? '.' . $ext : '');
    $dest = $uploadsDir . '/' . $safe;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['error' => 'Erreur lors du déplacement du fichier.'];
    }

    return ['path' => 'view/uploads/' . $safe];
}

/**
 * Envoie une notification par email à l'auteur de l'avis quand une nouvelle réponse est ajoutée.
 * Utilise mail() et log en cas d'échec (configuration SMTP peut être nécessaire en local).
 */
function notifyAvisAuthorByEmail($db, $avis, $reponse)
{
    try {
        if (empty($avis) || empty($avis['email'])) return false;
        $to = $avis['email'];
        $avisId = $avis['id'] ?? '';
        $subject = "[SmartLancer] Nouvelle réponse à votre avis";
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $link = (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . $host . '/validationmodule/view/reponsefront.php?avis_id=' . $avisId;

        $message = "Bonjour " . ($avis['nom'] ?? '') . ",\n\n";
        $message .= "Une nouvelle réponse a été ajoutée à votre avis (ID: " . $avisId . ").\n\n";
        $message .= "Auteur: " . ($reponse['nom'] ?? 'Anonyme') . "\n";
        $message .= "Contenu:\n" . ($reponse['contenu'] ?? '') . "\n\n";
        $message .= "Voir la réponse et l'avis : " . $link . "\n\n";
        $message .= "Cordialement,\nL'équipe SmartLancer";

        $headers = "From: no-reply@" . $host . "\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

        // Check global setting
        $enabled = getAppSetting($db, 'notifications_enabled', '1');
        $sent = 0;
        if ($enabled == '1') {
            $ok = @mail($to, $subject, $message, $headers);
            if ($ok) {
                $sent = 1;
                error_log("[notifyAvisAuthorByEmail] Mail envoyé à: $to");
            } else {
                error_log("[notifyAvisAuthorByEmail] Envoi du mail échoué vers: $to");
            }
        } else {
            error_log('[notifyAvisAuthorByEmail] Notifications désactivées par le réglage global.');
        }

        // Store notification record
        try {
            $stmt = $db->prepare('INSERT INTO notifications (avis_id, reponse_id, to_email, subject, body, sent) VALUES (:avis_id, :reponse_id, :to_email, :subject, :body, :sent)');
            $rid = $reponse['id'] ?? null;
            $stmt->bindParam(':avis_id', $avisId);
            $stmt->bindParam(':reponse_id', $rid);
            $stmt->bindParam(':to_email', $to);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':body', $message);
            $stmt->bindParam(':sent', $sent, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            error_log('[notifyAvisAuthorByEmail] Erreur enregistrement notification: ' . $e->getMessage());
        }

        return $sent == 1;
    } catch (Exception $e) {
        error_log('[notifyAvisAuthorByEmail] Exception: ' . $e->getMessage());
        return false;
    }
}

if ($action === 'add') {
    $avis_id = isset($_POST['avis_id']) ? (int)$_POST['avis_id'] : 0;
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $role_repondeur = trim($_POST['role_repondeur'] ?? 'client');
    // statut géré uniquement en backoffice; pour les nouvelles réponses on force 'en_attente'
    $statut = 'en_attente';
    $visible = isset($_POST['visible']) ? (int)$_POST['visible'] : 1;
    $visible = $visible ? 1 : 0;
    $type = trim($_POST['type'] ?? 'freelance');
    
    // New fields for PRO form
    $categorie = trim($_POST['categorie'] ?? '');

    // file upload (optional)
    $piece_jointe = null;
    if (isset($_FILES['piece_jointe'])) {
        error_log('Fichier reçu: ' . print_r($_FILES['piece_jointe'], true));
        $upload = uploadPieceJointe($_FILES['piece_jointe'], $ALLOWED_MIMES, $MAX_FILE_SIZE);
        if (is_array($upload) && isset($upload['error'])) {
            error_log('Erreur upload: ' . $upload['error']);
            $result['message'] = $upload['error'];
            echo json_encode($result);
            exit;
        }
        if (is_array($upload) && isset($upload['path'])) {
            $piece_jointe = $upload['path'];
            error_log('Fichier uploadé avec succès: ' . $piece_jointe);
        }
    } else {
        error_log('Aucun fichier dans FILES');
    }

    if ($avis_id <= 0 || $nom === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $contenu === '') {
        $result['message'] = 'Données invalides.';
        echo json_encode($result);
        exit;
    }

    if (!in_array($role_repondeur, $ALLOWED_ROLES, true)) $role_repondeur = 'client';
    if (!in_array($type, $ALLOWED_TYPES, true)) $type = 'freelance';

    // Note: piece_jointe and categorie are optional fields
    error_log('Avant addReponse - piece_jointe: ' . ($piece_jointe ?? 'NULL') . ', categorie: ' . ($categorie ?? 'NULL'));
    
    if ($reponseModel->addReponse($avis_id, $nom, $email, $contenu, $visible, $type, $role_repondeur, $statut, $is_online, $piece_jointe, $categorie)) {
        $id = $db->lastInsertId();
        $new = $reponseModel->getById($id);
        error_log('Après getById - reponse: ' . json_encode($new));

        // Notification automatique: récupérer l'avis et prévenir l'auteur
        try {
            $avis = null;
            if (isset($avisModel)) {
                $avis = $avisModel->getAvisById($avis_id);
            }
            // envoyer email automatiquement (même si le formulaire ne contient plus la case notifier)
            if ($avis) {
                notifyAvisAuthorByEmail($db, $avis, $new);
            }
        } catch (Exception $e) {
            error_log('[reponsecontroller] Erreur notification: ' . $e->getMessage());
        }

        $result = ['success' => true, 'message' => 'Réponse ajoutée', 'reponse' => $new];
    } else {
        $result['message'] = 'Erreur en base.';
    }

    echo json_encode($result);
    exit;
}

if ($action === 'edit') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $role_repondeur = isset($_POST['role_repondeur']) ? trim($_POST['role_repondeur']) : null;
    $visible = array_key_exists('visible', $_POST) ? (int)$_POST['visible'] : null;
    if ($visible !== null) $visible = $visible ? 1 : 0;
    $type = isset($_POST['type']) ? trim($_POST['type']) : null;
    
    // New fields for PRO form
    $categorie = isset($_POST['categorie']) ? trim($_POST['categorie']) : null;

    $piece_jointe = null;
    if (isset($_FILES['piece_jointe']) && $_FILES['piece_jointe']['size'] > 0) {
        $upload = uploadPieceJointe($_FILES['piece_jointe'], $ALLOWED_MIMES, $MAX_FILE_SIZE);
        if (is_array($upload) && isset($upload['error'])) {
            $result['message'] = $upload['error'];
            echo json_encode($result);
            exit;
        }
        if (is_array($upload) && isset($upload['path'])) {
            $piece_jointe = $upload['path'];
        }
    }

    if ($id <= 0 || $nom === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $contenu === '') {
        $result['message'] = 'Données invalides.';
        echo json_encode($result);
        exit;
    }

    $existing = $reponseModel->getById($id);
    if (!$existing) {
        $result['message'] = 'Réponse introuvable.';
        echo json_encode($result);
        exit;
    }

    if (strcasecmp($existing['email'], $email) !== 0) {
        $result['message'] = 'Email non autorisé pour modifier cette réponse.';
        echo json_encode($result);
        exit;
    }

    if ($type !== null && !in_array($type, $ALLOWED_TYPES, true)) $type = 'freelance';
    if ($role_repondeur !== null && !in_array($role_repondeur, $ALLOWED_ROLES, true)) $role_repondeur = null;

    if ($reponseModel->updateReponse($id, $nom, $email, $contenu, $visible, $type, $role_repondeur, null)) {
        $updated = $reponseModel->getById($id);
        $result = ['success' => true, 'message' => 'Réponse modifiée', 'reponse' => $updated];
    } else {
        $result['message'] = 'Erreur lors de la mise à jour.';
    }

    echo json_encode($result);
    exit;
}

if ($action === 'delete') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $email = trim($_POST['email'] ?? '');

    if ($id <= 0 || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result['message'] = 'Identifiant ou email invalide.';
        echo json_encode($result);
        exit;
    }

    $existing = $reponseModel->getById($id);
    if (!$existing) {
        $result['message'] = 'Réponse introuvable.';
        echo json_encode($result);
        exit;
    }

    if (strcasecmp($existing['email'], $email) !== 0) {
        $result['message'] = 'Email non autorisé pour supprimer cette réponse.';
        echo json_encode($result);
        exit;
    }

    if ($reponseModel->deleteById($id)) {
        $result = ['success' => true, 'message' => 'Réponse supprimée'];
    } else {
        $result['message'] = 'Erreur lors de la suppression.';
    }

    echo json_encode($result);
    exit;
}

// Actions réservées au backoffice (admin) : changer visibilité / suppression sans email
if ($action === 'admin_set_visible') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $visible = isset($_POST['visible']) ? (int)$_POST['visible'] : null;
    if ($id <= 0 || ($visible !== 0 && $visible !== 1)) {
        $result['message'] = 'Paramètres invalides.';
        echo json_encode($result);
        exit;
    }

    $existing = $reponseModel->getById($id);
    if (!$existing) {
        $result['message'] = 'Réponse introuvable.';
        echo json_encode($result);
        exit;
    }

    $nom = $existing['nom'];
    $email = $existing['email'];
    $contenu = $existing['contenu'];

    if ($reponseModel->updateReponse($id, $nom, $email, $contenu, $visible, null)) {
        $updated = $reponseModel->getById($id);
        $result = ['success' => true, 'message' => 'Visibilité mise à jour', 'reponse' => $updated];
    } else {
        $result['message'] = 'Erreur lors de la mise à jour.';
    }

    echo json_encode($result);
    exit;
}

if ($action === 'admin_delete') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        $result['message'] = 'Identifiant invalide.';
        echo json_encode($result);
        exit;
    }

    $existing = $reponseModel->getById($id);
    if (!$existing) {
        $result['message'] = 'Réponse introuvable.';
        echo json_encode($result);
        exit;
    }

    if ($reponseModel->deleteById($id)) {
        $result = ['success' => true, 'message' => 'Réponse supprimée (admin)'];
    } else {
        $result['message'] = 'Erreur lors de la suppression.';
    }

    echo json_encode($result);
    exit;
}

// Gestion du statut en ligne/hors ligne
if ($action === 'set_online_status') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $is_online = isset($_POST['is_online']) ? (int)$_POST['is_online'] : 0;
    $email = trim($_POST['email'] ?? '');

    if ($id <= 0 || $email === '') {
        $result['message'] = 'Données invalides.';
        echo json_encode($result);
        exit;
    }

    $existing = $reponseModel->getById($id);
    if (!$existing) {
        $result['message'] = 'Réponse introuvable.';
        echo json_encode($result);
        exit;
    }

    if (strcasecmp($existing['email'], $email) !== 0) {
        $result['message'] = 'Email non autorisé.';
        echo json_encode($result);
        exit;
    }

    if ($reponseModel->setOnlineStatus($id, $is_online ? true : false)) {
        $updated = $reponseModel->getById($id);
        $result = ['success' => true, 'message' => 'Statut en ligne mis à jour', 'reponse' => $updated];
    } else {
        $result['message'] = 'Erreur lors de la mise à jour.';
    }

    echo json_encode($result);
    exit;
}

echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage(),
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

