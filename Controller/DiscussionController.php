<?php
require_once __DIR__ . '/../Model/Message.php';
require_once __DIR__ . '/../Model/Reclamation.php';

class DiscussionController {
    private $messageModel;
    private $reclamationModel;

    public function __construct() {
        $this->messageModel = new Message();
        $this->reclamationModel = new Reclamation();
    }

    public function getDiscussion($id_reclamation) {
        return $this->messageModel->getMessagesByReclamation($id_reclamation);
    }

    public function sendMessage($id_reclamation, $sender, $contenu) {
        $rec = $this->reclamationModel->showReclamation($id_reclamation);
        if (!$rec) return false;

        // Vérifier si la discussion est fermée
        if ($rec['status'] === 'Fermée') return false;

        // Mettre le statut à "En cours" si c'est la première réponse
        if ($rec['status'] === 'En attente') {
            $this->reclamationModel->updateStatus($id_reclamation, 'En cours');
        }

        return $this->messageModel->addMessage($id_reclamation, $sender, $contenu);
    }

    public function closeDiscussion($id_reclamation) {
        return $this->reclamationModel->updateStatus($id_reclamation, 'Fermée');
    }

    public function isClosed($id_reclamation) {
        $rec = $this->reclamationModel->showReclamation($id_reclamation);
        return $rec && $rec['status'] === 'Fermée';
    }
}
?>
