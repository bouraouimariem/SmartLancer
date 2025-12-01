<?php
require_once "../../Controller/ReclamationController.php";

if(isset($_GET['id'])) {
    $controller = new ReclamationController();
    $controller->deleteResponse($_GET['id']);
    header("Location: listReponses.php");
    exit;
}
?>
