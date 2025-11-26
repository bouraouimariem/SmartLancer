<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/reponse.php';

$db = (new Database())->getConnection();
$reponseModel = new Reponse($db);

$action = isset($_POST['action']) ? $_POST['action'] : '';

$result = ['success' => false, 'message' => 'Action non spécifiée'];

if ($action === 'add') {
    $avis_id = isset($_POST['avis_id']) ? (int)$_POST['avis_id'] : 0;
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';

    if ($avis_id <= 0 || $nom === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $contenu === '') {
        $result['message'] = 'Données invalides.';
        echo json_encode($result);
        exit;
    }

    if ($reponseModel->addReponse($avis_id, $nom, $email, $contenu)) {
        $id = $db->lastInsertId();
        $new = $reponseModel->getById($id);
        $result = ['success' => true, 'message' => 'Réponse ajoutée', 'reponse' => $new];
    } else {
        $result['message'] = 'Erreur en base.';
    }

    echo json_encode($result);
    exit;
}

if ($action === 'edit') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';

    if ($id <= 0 || $nom === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $contenu === '') {
        $result['message'] = 'Données invalides.';
        echo json_encode($result);
        exit;
    }

    // Verify that the provided email matches the author of the stored response
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

    if ($reponseModel->updateReponse($id, $nom, $email, $contenu)) {
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
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if ($id <= 0 || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result['message'] = 'Identifiant ou email invalide.';
        echo json_encode($result);
        exit;
    }

    // verify owner
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

echo json_encode($result);
