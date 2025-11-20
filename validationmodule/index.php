<?php
require_once '../validationmodule/controller/aviscontroller.php';

$controller = new aviscontroller();

$page = $_GET['page'] ?? 'profil';

switch ($page) {
    case 'profil':
        $controller->showProfil();
        break;

    case 'avis':
        $controller->showAvis();
        break;

    case 'add':
        $controller->addAvis();
        break;

    default:
        $controller->showProfil();
}
