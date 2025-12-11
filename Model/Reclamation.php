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
    // 1️⃣ Ajouter la réclamation
    $query = "INSERT INTO reclamation (nom, email, sujet, message, telephone, status, date_envoi)
              VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$nom, $email, $sujet, $message, $telephone, $status]);

    $id_reclamation = $this->conn->lastInsertId();

    // 2️⃣ Ajouter une notification pour l'admin
    require_once __DIR__ . '/Notification.php';
    $notification = new Notification();
    $notification->add(
        $id_reclamation,
        'admin', // destinataire
        "Nouvelle réclamation envoyée par $nom : $sujet"
    );

    return $id_reclamation;
}


    public function deleteReclamation($id_reclamation) {

    // 1️⃣ Supprimer les messages liés
    $stmt = $this->conn->prepare("DELETE FROM messages WHERE id_reclamation = ?");
    $stmt->execute([$id_reclamation]);

    // 2️⃣ Supprimer les réponses liées
    $stmt = $this->conn->prepare("DELETE FROM reponses WHERE id_reclamation = ?");
    $stmt->execute([$id_reclamation]);

    // 3️⃣ Supprimer la réclamation
    $stmt = $this->conn->prepare("DELETE FROM reclamation WHERE id_reclamation = ?");
    $stmt->execute([$id_reclamation]);
}


    public function updateReclamation($id_reclamation, $nom, $email, $sujet, $message, $telephone, $status = null) {
        if ($status !== null) {
            $query = "UPDATE reclamation SET nom = ?, email = ?, sujet = ?, message = ?, telephone = ?, status = ? WHERE id_reclamation = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nom, $email, $sujet, $message, $telephone, $status, $id_reclamation]);
        } else {
            $query = "UPDATE reclamation SET nom = ?, email = ?, sujet = ?, message = ?, telephone = ? WHERE id_reclamation = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nom, $email, $sujet, $message, $telephone, $id_reclamation]);
        }
    }

    public function getReclamation($id_reclamation) {
        $stmt = $this->conn->prepare("SELECT * FROM reclamation WHERE id_reclamation = ?");
        $stmt->execute([$id_reclamation]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
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
                SELECT r.id_reclamation, r.nom, r.email, r.sujet, r.message, r.status, r.date_envoi, r.telephone,
                       rep.id_reponse, rep.contenu, rep.date_reponse
                FROM reclamation r
                LEFT JOIN reponses rep ON r.id_reclamation = rep.id_reclamation
                WHERE r.email = ?
                ORDER BY r.date_envoi DESC
            ");
            $stmt->execute([$email]);
        } else {
            $stmt = $this->conn->prepare("
                SELECT r.id_reclamation, r.nom, r.email, r.sujet, r.message, r.status, r.date_envoi, r.telephone,
                       rep.id_reponse, rep.contenu, rep.date_reponse
                FROM reclamation r
                LEFT JOIN reponses rep ON r.id_reclamation = rep.id_reclamation
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
    public function filterReclamations($filters = [], $sort = 'date_envoi DESC', $email = null) {
    $sql = "SELECT * FROM reclamation WHERE 1=1";
    $params = [];

    // Filtre par status
    if (!empty($filters['status'])) {
        $sql .= " AND status = :status";
        $params[':status'] = $filters['status'];
    }

    // Filtre par date
    if (!empty($filters['date'])) {
        $sql .= " AND DATE(date_envoi) = :date";
        $params[':date'] = $filters['date'];
    }

    // Filtre par ID, nom ou email
if (!empty($filters['search'])) {
    $sql .= " AND (id_reclamation LIKE :search OR nom LIKE :search OR email LIKE :search)";
    $params[':search'] = "%" . $filters['search'] . "%";
}

// Filtre spécifique par nom
if (!empty($filters['nom'])) {
    $sql .= " AND nom LIKE :nom";
    $params[':nom'] = "%" . $filters['nom'] . "%";
}

// Filtre spécifique par email
if (!empty($filters['email'])) {
    $sql .= " AND email LIKE :email";
    $params[':email'] = "%" . $filters['email'] . "%";
}


    // Tri
    $sql .= " ORDER BY $sort";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
   


}
?>
