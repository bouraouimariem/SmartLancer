<?php
require_once __DIR__ . '/../config.php';

class NotificationUser {
    private $conn;

    public function __construct() {
        $config = new Config();
        $this->conn = $config->getConnexion();
    }

    // Ajouter une notification
    public function add($id_reponse, $id_reclamation, $message) {
        $stmt = $this->conn->prepare("
            INSERT INTO notifications_user (id_reponse, id_reclamation, message, is_read, date)
            VALUES (?, ?, ?, 0, NOW())
        ");
        return $stmt->execute([$id_reponse, $id_reclamation, $message]);
    }

    // Afficher toutes les notifications
    public function getAll() {
        $stmt = $this->conn->query("
            SELECT * FROM notifications_user 
            ORDER BY date DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marquer comme lue
    public function markAsRead($id_notification) {
        $stmt = $this->conn->prepare("
            UPDATE notifications_user SET is_read = 1 WHERE id_notification = ?
        ");
        return $stmt->execute([$id_notification]);
    }

    // Supprimer notification
    public function delete($id_notification) {
        $stmt = $this->conn->prepare("
            DELETE FROM notifications_user WHERE id_notification = ?
        ");
        return $stmt->execute([$id_notification]);
    }
}
