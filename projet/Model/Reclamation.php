<?php
require_once __DIR__ . '/../config.php';

class Reclamation {
    private $conn;

    public function __construct() {
        $config = new Config();
        $this->conn = $config->getConnexion();
    }

    // Lister toutes les réclamations
    public function listReclamations() {
        $stmt = $this->conn->query("SELECT * FROM reclamation ORDER BY date_envoi DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter une réclamation
    public function addReclamation($nom, $email, $sujet, $message) {
        $stmt = $this->conn->prepare("INSERT INTO reclamation (nom, email, sujet, message, date_envoi) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$nom, $email, $sujet, $message]);
    }

    // Supprimer une réclamation
    public function deleteReclamation($id) {
        $stmt = $this->conn->prepare("DELETE FROM reclamation WHERE id = ?");
        $stmt->execute([$id]);
    }

    // Modifier une réclamation
    public function updateReclamation($id, $nom, $email, $sujet, $message) {
        $stmt = $this->conn->prepare("UPDATE reclamation SET nom = ?, email = ?, sujet = ?, message = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $sujet, $message, $id]);
    }

    // Récupérer une réclamation par ID
    public function getReclamation($id) {
        $stmt = $this->conn->prepare("SELECT * FROM reclamation WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer l'ID de la dernière réclamation
    public function getLastId() {
        return $this->conn->lastInsertId();
    }

    // Répondre à une réclamation
    public function replyReclamation($id, $response) {
        $stmt = $this->conn->prepare("UPDATE reclamation SET reponse = ? WHERE id = ?");
        $stmt->execute([$response, $id]);
    }
}
