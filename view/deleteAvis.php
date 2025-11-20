<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/avis.php';

if (!isset($_GET['id'])) {
    header('Location: /validationmodule/view/profilfreelancer.php');
    exit;
}

$id = (int)$_GET['id'];
$pdo = (new Database())->getConnection();
$avisModel = new Avis($pdo);

if ($avisModel->deleteAvis($id)) {
    header('Location: /validationmodule/view/profilfreelancer.php');
    exit;
} else {
    echo "Erreur lors de la suppression de l'avis.";
}
