<?php
require_once __DIR__ . '/../config.php';

class Notification {
    private $conn;

    public function __construct() {
        $config = new Config();
        $this->conn = $config->getConnexion();
    }

    public function add($id_reclamation, $recipient, $message) {
        $stmt = $this->conn->prepare(
            "INSERT INTO notifications (id_reclamation, recipient, message, is_read, date) VALUES (?, ?, ?, 0, NOW())"
        );
        return $stmt->execute([$id_reclamation, $recipient, $message]);
    }

    public function getAll($recipient) {
        $stmt = $this->conn->prepare("SELECT * FROM notifications WHERE recipient=? ORDER BY date DESC");
        $stmt->execute([$recipient]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUnread($recipient) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM notifications WHERE recipient=? AND is_read=0");
        $stmt->execute([$recipient]);
        return $stmt->fetchColumn();
    }

    public function markAsRead($id) {
        $stmt = $this->conn->prepare("UPDATE notifications SET is_read=1 WHERE id_notification=?");
        return $stmt->execute([$id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM notifications WHERE id_notification=?");
        return $stmt->execute([$id]);
    }
}
