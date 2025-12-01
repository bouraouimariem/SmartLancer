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

    public function sendMessage($id_reclamation, $sender, $contenu = '', $file = null) {
        $filename = null;

        if ($file && isset($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
            $uploadsDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0775, true);

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . "." . $ext;
            move_uploaded_file($file['tmp_name'], $uploadsDir . $filename);
        }

        // Ne rien envoyer si contenu et fichier sont vides
        if (empty($contenu) && empty($filename)) return false;

        return $this->messageModel->addMessage($id_reclamation, $sender, $contenu, $filename);
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
