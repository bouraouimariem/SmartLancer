<?php
require_once __DIR__ . '/../model/database.php';

class Portfolio {

    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO portfolio 
                (id_utilisateur, lien, photo, bio, experience, competence, tarif, created_at)
                VALUES (:id_utilisateur, :lien, :photo, :bio, :experience, :competence, :tarif, NOW())";
    
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id_utilisateur' => $data['id_utilisateur'],
            ':lien'           => $data['lien'],
            ':photo'          => $data['photo'],
            ':bio'            => $data['bio'],
            ':experience'     => $data['experience'],
            ':competence'     => $data['competence'],
            ':tarif'          => $data['tarif'],
        ]);
    }

    public function getByUserId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM portfolio WHERE id_utilisateur = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function updatePortfolio($data) {
    $sql = "UPDATE portfolio 
            SET photo = :photo,
                lien = :lien,
                bio = :bio,
                experience = :experience,
                competence = :competence,
                tarif = :tarif
            WHERE id_utilisateur = :id_utilisateur";

    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($data);
}

public function getAllFreelancers() {
    $sql = "SELECT p.*, u.name, u.email 
            FROM portfolio p 
            JOIN us u ON p.id_utilisateur = u.id 
            ORDER BY u.name ASC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}
