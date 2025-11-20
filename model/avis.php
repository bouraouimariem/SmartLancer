<?php
class Avis {
    private $conn;
    private $table = "avis";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ajouter un avis
    public function addAvis($nom, $email, $note, $contenu) {
        $query = "INSERT INTO {$this->table} (nom, email, note, contenu) VALUES (:nom, :email, :note, :contenu)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':contenu', $contenu);
        return $stmt->execute();
    }

    // Récupérer tous les avis
    public function getAllAvis() {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un avis par son id
    public function getAvisById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Supprimer un avis
    public function deleteAvis($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Modifier un avis
    public function updateAvis($id, $nom, $email, $note, $contenu) {
        $query = "UPDATE {$this->table} SET nom = :nom, email = :email, note = :note, contenu = :contenu WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Compter les likes pour un avis
    public function getLikesCount($avis_id) {
        $query = "SELECT COUNT(*) FROM avis_likes WHERE avis_id = :avis_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Vérifier si un utilisateur a déjà liké
    public function hasUserLiked($avis_id, $email) {
        $query = "SELECT COUNT(*) FROM avis_likes WHERE avis_id = :avis_id AND email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Ajouter un like
    public function addLike($avis_id, $email) {
        if ($this->hasUserLiked($avis_id, $email)) {
            return false;
        }
        $query = "INSERT INTO avis_likes (avis_id, email) VALUES (:avis_id, :email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }
}
