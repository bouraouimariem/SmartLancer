<?php
include '../../../../controller/propositionC.php';

$propoC = new propositionController();
$propoC->update_propo_stat($_GET["id_propo"],'en cours');

header('location: ../projet_client.php');
?>
