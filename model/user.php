<?php
// model/User.php
require_once __DIR__ . '/../model/database.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    // Create (register)
    public function create(string $name, string $email, string $passwordHash, string $role): bool {
        $sql = "INSERT INTO us (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $passwordHash,
            ':role' => $role
        ]);
    }

    // Read by email
    public function findByEmail(string $email) {
        $sql = "SELECT * FROM us WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(); // false | assoc array
    }

    

    

    public function getAllUsers() {
    $stmt = $this->pdo->query("SELECT id, email, role, created_at, banned FROM us");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function deleteUser($id) {
    $stmt = $this->pdo->prepare("DELETE FROM us WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}


public function setBanStatus($id, $status) {
    $stmt = $this->pdo->prepare("UPDATE us SET banned = :status WHERE id = :id");
    return $stmt->execute(['status' => $status, 'id' => $id]);
}

public function storeResetToken($email, $token) {
    $sql = "UPDATE us SET reset_token = :token WHERE email = :email";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute(['token' => $token, 'email' => $email]);
}


public function verifyToken($token) {
    $sql = "SELECT email FROM us WHERE reset_token = :token LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['token' => $token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['email'] ?? false;
}


public function updatePassword($email, $password) {
    $sql = "UPDATE us SET password = :password, reset_token = NULL WHERE email = :email";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute(['password' => $password, 'email' => $email]);
}



}
