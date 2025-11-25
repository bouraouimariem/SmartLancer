<?php
include '../../../../controller/propositionC.php';

$propoC = new propositionController();
$propoC->delete_propo($_GET["id_propo"]);

header('location: ../projet_client.php');
?>
