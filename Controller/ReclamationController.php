<?php
require_once __DIR__ . '/../Model/Reclamation.php';

class ReclamationController {
    private $model;

    public function __construct() {
        $this->model = new Reclamation();
    }

    // Liste toutes les réclamations ou celles d'un utilisateur
    public function list($email = null) {
        return $this->model->listReclamations($email);
    }

    // Ajout d'une réclamation
    public function add($nom, $email, $sujet, $message, $status = 'En attente') {
        return $this->model->addReclamation($nom, $email, $sujet, $message, $status);
    }

    // Suppression
    public function delete($id) {
        $this->model->deleteReclamation($id);
    }

    // Modification
    public function update($id, $nom, $email, $sujet, $message, $status = null) {
        $this->model->updateReclamation($id, $nom, $email, $sujet, $message, $status);
    }

    // Récupérer une réclamation par ID
    public function get($id) {
        return $this->model->getReclamation($id);
    }

    // Répondre à une réclamation
    public function reply($id, $response) {
        $this->model->replyReclamation($id, $response);
    }

    // Changer le statut uniquement
    public function changeStatut($id, $statut) {
        $this->model->updateStatut($id, $statut);
    }
}


$controller = new ReclamationController();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;

    switch ($action) {
        case "delete":
            if ($id) {
                $controller->delete($id);
                header("Location: ../view/backend/listReclamations.php");
                exit();
            }
            break;

        case "reply":
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $response = $_POST['response'];
                $controller->reply($id, $response);
                header("Location: ../view/backend/listReclamations.php");
                exit();
            }
            break;

        case "update":
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $nom = $_POST['nom'] ?? '';
                $email = $_POST['email'] ?? '';
                $sujet = $_POST['sujet'] ?? '';
                $message = $_POST['message'] ?? '';
                $status = $_POST['statut'] ?? null; // récupère le statut si fourni
                $controller->update($id, $nom, $email, $sujet, $message, $status);
                header("Location: ../view/backend/listReclamations.php");
                exit();
            }
            break;
    }
}
?>
