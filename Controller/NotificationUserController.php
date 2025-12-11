<?php
require_once __DIR__ . '/../Model/NotificationUser.php';


class NotificationUserController {
    private $model;

    public function __construct() {
        $this->model = new NotificationUser();
    }

    public function add($id_reponse, $id_reclamation, $recipient, $message) {
        return $this->model->add($id_reponse, $id_reclamation, $recipient, $message);
    }

    public function getAll($recipient) {
        return $this->model->getAll($recipient);
    }

    public function countUnread($recipient) {
        return $this->model->countUnread($recipient);
    }

    public function markAsRead($id) {
        return $this->model->markAsRead($id);
    }

    public function delete($id) {
        return $this->model->delete($id);
    }
}
?>
