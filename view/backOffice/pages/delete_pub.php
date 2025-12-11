<?php
include "../../../controller/publicationC.php";

if (!isset($_POST['id_pub'])) {
    die("ID manquant !");
}

$pubC = new publicationController();
$pubC->delete_pub($_POST['id_pub']);

header("Location: ../gestion_projet.php?delete=1");
exit;
?>
