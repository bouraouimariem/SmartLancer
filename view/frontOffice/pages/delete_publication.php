<?php
include '../../../controller/publicationC.php';

if (isset($_GET['id_pub'])) {
    $pubC = new publicationController();
    $pubC->delete_pub($_GET["id_pub"]);
}

// Retour vers la page des publications
header('Location: ../clientpub.php');
exit;
?>
