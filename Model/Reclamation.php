<?php
require_once __DIR__ . '/../config.php';

class Reclamation {

    private $conn;

    public function __construct() {
        $config = new Config();
        $this->conn = $config->getConnexion();
    }

    public function listReclamations($email = null) {
        if ($email) {
            $stmt = $this->conn->prepare("SELECT * FROM reclamation WHERE email = ? ORDER BY date_envoi DESC");
            $stmt->execute([$email]);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM reclamation ORDER BY date_envoi DESC");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addReclamation($nom, $email, $sujet, $message, $telephone, $status = 'En attente') {
        $query = "INSERT INTO reclamation (nom, email, sujet, message, telephone, status, date_envoi)
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$nom, $email, $sujet, $message, $telephone, $status]);
        return $this->conn->lastInsertId();
    }

    public function deleteReclamation($id_reclamation) {
        $stmt = $this->conn->prepare("DELETE FROM reclamation WHERE ID_reclamation = ?");
        $stmt->execute([$id_reclamation]);
    }

    public function updateReclamation($id_reclamation, $nom, $email, $sujet, $message, $telephone, $status = null) {
        if ($status !== null) {
            $query = "UPDATE reclamation SET nom = ?, email = ?, sujet = ?, message = ?, telephone = ?, status = ? WHERE ID_reclamation = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nom, $email, $sujet, $message, $telephone, $status, $id_reclamation]);
        } else {
            $query = "UPDATE reclamation SET nom = ?, email = ?, sujet = ?, message = ?, telephone = ? WHERE ID_reclamation = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nom, $email, $sujet, $message, $telephone, $id_reclamation]);
        }
    }

    public function getReclamation($id_reclamation) {
        $stmt = $this->conn->prepare("SELECT * FROM reclamation WHERE ID_reclamation = ?");
        $stmt->execute([$id_reclamation]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ⚡ Corrigé : status au lieu de statut
    public function updateStatus($id_reclamation, $status) {
        $sql = "UPDATE reclamation SET status = :status WHERE id_reclamation = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id_reclamation);

        return $stmt->execute();
    }

    public function listReclamationsWithResponses($email = null) {
        if ($email) {
            $stmt = $this->conn->prepare("
                SELECT r.ID_reclamation, r.nom, r.email, r.sujet, r.message, r.status, r.date_envoi, r.telephone,
                       rep.ID_reponse, rep.contenu, rep.date_reponse
                FROM reclamation r
                LEFT JOIN reponses rep ON r.ID_reclamation = rep.ID_reclamation
                WHERE r.email = ?
                ORDER BY r.date_envoi DESC
            ");
            $stmt->execute([$email]);
        } else {
            $stmt = $this->conn->prepare("
                SELECT r.ID_reclamation, r.nom, r.email, r.sujet, r.message, r.status, r.date_envoi, r.telephone,
                       rep.ID_reponse, rep.contenu, rep.date_reponse
                FROM reclamation r
                LEFT JOIN reponses rep ON r.ID_reclamation = rep.ID_reclamation
                ORDER BY r.date_envoi DESC
            ");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function showReclamation($id) {
        $sql = "SELECT * FROM reclamation WHERE id_reclamation = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
?>
