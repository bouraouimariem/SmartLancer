<?php
require_once "../../Controller/ReclamationController.php";
$controller = new ReclamationController();

$id = $_GET['id'] ?? null;
if ($id) $controller->delete($id);

header("Location: listReclamations.php");
exit;
