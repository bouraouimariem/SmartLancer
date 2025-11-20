<?php
require_once "../../Model/Reclamation.php";

$model = new Reclamation();

if (!isset($_GET['id'])) {
    die("ID manquant !");
}

$id = $_GET['id'];
$model->deleteReclamation($id);

// Rediriger vers index.php après suppression
header("Location: index.php");
exit;
