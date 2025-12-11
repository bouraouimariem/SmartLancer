<?php
require_once __DIR__ . '/../Model/Reclamation.php';
require_once __DIR__ . '/../Model/Reponse.php';

class ReclamationController {

    private $model;
    private $responseModel;

    public function __construct() {
        $this->model = new Reclamation();
        $this->responseModel = new Reponse();
    }

    /* -------------------------------
       RECLAMATIONS
    --------------------------------*/
    public function list($email = null) {
        return $this->model->listReclamations($email);
    }

    public function add($nom, $email, $sujet, $message, $telephone, $status = 'En attente') {
        return $this->model->addReclamation($nom, $email, $sujet, $message, $telephone, $status);
    }

    public function delete($id) {
        $this->model->deleteReclamation($id);
    }

    public function update($id, $nom, $email, $sujet, $message, $telephone, $status = null) {
        $this->model->updateReclamation($id, $nom, $email, $sujet, $message, $telephone, $status);
    }

    public function get($id) {
        return $this->model->getReclamation($id);
    }

    public function changeStatut($id, $statut) {
        return $this->model->updateStatus($id, $statut);
    }

    /* -------------------------------
       REPONSES / CHAT
    --------------------------------*/
   public function reply($id_reclamation, $contenu) {
    if (empty(trim($contenu))) return false;

    // 1️⃣ Ajouter la réponse
    $this->responseModel->addResponse($id_reclamation, $contenu);

    // 2️⃣ Récupérer l’ID de la réponse ajoutée
    $id_reponse = $this->responseModel->getLastInsertId();

    // 3️⃣ Créer une notification user
    require_once __DIR__ . '/../Model/NotificationUser.php';
    $notif = new NotificationUser();

    $messageNotif = "L'administrateur a répondu à votre réclamation #$id_reclamation.";

    $notif->add($id_reponse, $id_reclamation, $messageNotif);

    // 4️⃣ Mettre à jour le statut
    $this->model->updateStatus($id_reclamation, "Répondu");

    return true;
}


    public function userReply($id_reclamation, $contenu) {
        if (empty(trim($contenu))) return false;
        $this->responseModel->addResponse($id_reclamation, $contenu);
        $this->model->updateStatus($id_reclamation, "Répondu");
        return true;
    }

    public function getResponsesByReclamation($id_reclamation) {
        return $this->responseModel->getResponsesByReclamation($id_reclamation);
    }

    public function getAllResponses() {
        return $this->responseModel->getAllResponses();
    }

    public function getResponse($id) {
        return $this->responseModel->getResponseById($id);
    }

    public function updateResponse($id, $contenu) {
        $this->responseModel->updateResponse($id, $contenu);
    }

    public function deleteResponse($id) {
        $this->responseModel->deleteResponse($id);
    }

    public function listWithResponses($email = null) {
        return $this->model->listReclamationsWithResponses($email);
    }
     public function filterAndSearch($filters = [], $sort = 'date_envoi DESC', $email = null) {
    return $this->model->filterReclamations($filters, $sort, $email);
}

// Vérifier si une réclamation est fermée
public function isClosed($id_reclamation) {
    $rec = $this->model->getReclamation($id_reclamation);
    return $rec && $rec['status'] === 'Fermée';
}





}
?>
