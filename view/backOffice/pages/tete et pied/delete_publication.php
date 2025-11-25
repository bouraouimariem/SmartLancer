<?php
include '../../../../controller/publicationC.php';

$pubC = new publicationController();
$pubC->delete_pub($_GET["id_pub"]);

header('Location: ../projets.php');
?>
