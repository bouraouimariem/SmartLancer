<?php
class Reponse {
    private $conn;
    private $table = 'reponses';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addReponse($avis_id, $nom, $email, $contenu) {
        $query = "INSERT INTO {$this->table} (avis_id, nom, email, contenu) VALUES (:avis_id, :nom, :email, :contenu)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contenu', $contenu);
        return $stmt->execute();
    }

    public function getByAvisId($avis_id) {
        $query = "SELECT * FROM {$this->table} WHERE avis_id = :avis_id ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteByAvisId($avis_id) {
        $query = "DELETE FROM {$this->table} WHERE avis_id = :avis_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateReponse($id, $nom, $email, $contenu) {
        $query = "UPDATE {$this->table} SET nom = :nom, email = :email, contenu = :contenu WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteById($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

