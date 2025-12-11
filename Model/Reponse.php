<?php
require_once __DIR__ . '/../config.php';

class Reponse {
    private $conn;

    public function __construct() {
        $config = new Config();
        $this->conn = $config->getConnexion();
    }

    // Récupérer toutes les réponses d'une réclamation (réponse admin)
    public function getResponsesByReclamation($id_reclamation) {
        $stmt = $this->conn->prepare("SELECT * FROM reponses WHERE id_reclamation = ? ORDER BY date_reponse ASC");
        $stmt->execute([$id_reclamation]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter une réponse admin
    public function addResponse($id_reclamation, $contenu) {
        $stmt = $this->conn->prepare("INSERT INTO reponses (id_reclamation, contenu, date_reponse) VALUES (?, ?, NOW())");
        return $stmt->execute([$id_reclamation, $contenu]);
    }

    // Pour compatibilité, méthodes getAllResponses() et getResponseById()
    public function getAllResponses() {
        $stmt = $this->conn->prepare("SELECT * FROM reponses ORDER BY date_reponse DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResponseById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM reponses WHERE id_reponse = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateResponse($id, $contenu) {
        $stmt = $this->conn->prepare("UPDATE reponses SET contenu = ? WHERE id_reponse = ?");
        return $stmt->execute([$contenu, $id]);
    }

    public function deleteResponse($id) {
        $stmt = $this->conn->prepare("DELETE FROM reponses WHERE id_reponse = ?");
        return $stmt->execute([$id]);
    }
    public function getLastInsertId() {
    return $this->conn->lastInsertId();
}

}
?>
