<?php
require_once __DIR__ . '/../Model/Reclamation.php';

class ReclamationController {
    private $model;

    public function __construct() {
        $this->model = new Reclamation();
    }

    public function list() {
        return $this->model->listReclamations();
    }

    public function add($nom, $email, $sujet, $message) {
        $this->model->addReclamation($nom, $email, $sujet, $message);
    }

    public function delete($id) {
        $this->model->deleteReclamation($id);
    }

    public function update($id, $nom, $email, $sujet, $message) {
        $this->model->updateReclamation($id, $nom, $email, $sujet, $message);
    }

    public function get($id) {
        return $this->model->getReclamation($id);
    }

    public function reply($id, $response) {
        $this->model->replyReclamation($id, $response);
    }
    
}

// Gestion des actions depuis l'URL
$controller = new ReclamationController();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;

    switch ($action) {
        case "delete":
            if ($id) {
                $controller->delete($id);
                header("Location: ../View/backend/listReclamations.php");
                exit();
            }
            break;

        case "reply":
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $response = $_POST['response'];
                $controller->reply($id, $response);
                header("Location: ../View/backend/listReclamations.php");
                exit();
            }
            break;

        case "update":
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $controller->update($id, $_POST['nom'], $_POST['email'], $_POST['sujet'], $_POST['message']);
                header("Location: ../View/backend/listReclamations.php");
                exit();
            }
            break;
    }
}
