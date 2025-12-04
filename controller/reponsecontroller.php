<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../model/database.php';
    require_once __DIR__ . '/../model/reponse.php';

    $db = (new Database())->getConnection();
    $reponseModel = new Reponse($db);

    $action = $_POST['action'] ?? '';

    $result = ['success' => false, 'message' => 'Action non spécifiée'];

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
    $notifier_auteur = isset($_POST['notifier_auteur']) ? (int)$_POST['notifier_auteur'] : 0;

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

    // Note: piece_jointe, categorie, and notifier_auteur are optional fields
    error_log('Avant addReponse - piece_jointe: ' . ($piece_jointe ?? 'NULL') . ', categorie: ' . ($categorie ?? 'NULL'));
    
    if ($reponseModel->addReponse($avis_id, $nom, $email, $contenu, $visible, $type, $role_repondeur, $statut, $is_online, $piece_jointe, $categorie, $notifier_auteur)) {
        $id = $db->lastInsertId();
        $new = $reponseModel->getById($id);
        error_log('Après getById - reponse: ' . json_encode($new));
        
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
    $notifier_auteur = isset($_POST['notifier_auteur']) ? (int)$_POST['notifier_auteur'] : 0;

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

