<?php
// view/frontOffice/pages/update_publication.php
include_once '../../../controller/publicationC.php';
include_once '../../../model/publication.php';
    

$pubController = new publicationController();

if (!isset($_POST['submit_modif'])) {
    header('Location: ../clientpub.php');
    exit;
}

// vérifier présence des champs essentiels
if (
    !isset($_POST['id_pub']) ||
    !isset($_POST['nom_pub_modif']) ||
    !isset($_POST['description_modif']) ||
    !isset($_POST['budget_modif']) ||
    !isset($_POST['delai_modif'])
) {
    die("Champs manquants.");
}

$id_pub = (int) $_POST['id_pub'];

// récupérer la publication existante pour obtenir id_user et date_pub/status
$existing = $pubController->getPublicationById($id_pub);
if (!$existing) {
    die("Publication introuvable.");
}

// convertir date_pub existante en DateTime (le modèle attend DateTime)
$date_pub = null;
if (!empty($existing['date_pub'])) {
    try {
        $date_pub = new DateTime($existing['date_pub']);
    } catch (Exception $e) {
        $date_pub = new DateTime(); // fallback
    }
}

// Construire l'objet Publications (conforme à ton modèle)
$pub = new Publications(
    $id_pub,
    (int)$existing['id_user'],
    $_POST['nom_pub_modif'],
    $_POST['categorie'] ?? $existing['categorie'],
    $_POST['description_modif'],
    (float) $_POST['budget_modif'],
    (string) $_POST['delai_modif'],
    $date_pub,
    $existing['status'] ?? 'en cours'
);

$pubController->updatepub($pub, $id_pub);

// redirection vers la liste
header('Location: ../clientpub.php');
exit;
