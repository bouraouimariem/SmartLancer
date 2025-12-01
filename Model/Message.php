<?php
require_once __DIR__ . '/../config.php';

class Message {

    private $conn;

    public function __construct() {
        $config = new Config();
        $this->conn = $config->getConnexion();
    }

    // Ajouter un message dans le chat
    public function addMessage($id_reclamation, $sender, $contenu, $fichier = null) {
    $sql = "INSERT INTO messages (id_reclamation, sender, contenu, fichier) 
            VALUES (:id_reclamation, :sender, :contenu, :fichier)";
    $stmt = $this->conn->prepare($sql);

    $stmt->bindValue(':id_reclamation', $id_reclamation);
    $stmt->bindValue(':sender', $sender);
    $stmt->bindValue(':contenu', $contenu);
    $stmt->bindValue(':fichier', $fichier);

    return $stmt->execute();
}


    // Récupérer tous les messages d'une discussion
    public function getMessagesByReclamation($id_reclamation) {
        $sql = "SELECT * FROM messages 
                WHERE id_reclamation = :id_reclamation 
                ORDER BY date_message ASC";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':id_reclamation', $id_reclamation);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
