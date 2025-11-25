<?php
require_once __DIR__ . '/../config.php';

class Reclamation {

    private $conn;

    public function __construct() {
        $config = new Config();
        $this->conn = $config->getConnexion();
    }

    /* --------------------  LISTE  --------------------- */
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

    /* --------------------  AJOUT  --------------------- */
    public function addReclamation($nom, $email, $sujet, $message, $status = 'En attente') {
        $query = "INSERT INTO reclamation (nom, email, sujet, message, status, date_envoi) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$nom, $email, $sujet, $message, $status]);
        return $this->conn->lastInsertId(); 
    }

    /* --------------------  SUPPRESSION  --------------------- */
    public function deleteReclamation($id) {
        $stmt = $this->conn->prepare("DELETE FROM reclamation WHERE id = ?");
        $stmt->execute([$id]);
    }

    /* --------------------  MODIFICATION  --------------------- */
    public function updateReclamation($id, $nom, $email, $sujet, $message, $status = null) {
        if ($status !== null) {
            $query = "UPDATE reclamation SET nom=?, email=?, sujet=?, message=?, status=? WHERE id=?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nom, $email, $sujet, $message, $status, $id]);
        } else {
            $query = "UPDATE reclamation SET nom=?, email=?, sujet=?, message=? WHERE id=?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nom, $email, $sujet, $message, $id]);
        }
    }

    /* --------------------  GET BY ID  --------------------- */
    public function getReclamation($id) {
        $stmt = $this->conn->prepare("SELECT * FROM reclamation WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* --------------------  REPONSE  --------------------- */
    public function replyReclamation($id, $reponse) {
        $query = "UPDATE reclamation 
                  SET reponse = ?, status = 'Répondu' 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$reponse, $id]);
    }

    /* --------------------  CHANGER STATUT  --------------------- */
    public function updateStatus($id, $status) {
        $query = "UPDATE reclamation SET status=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$status, $id]);
    }
}
?>
