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

    public function getUserById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM us WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
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

public function saveBanToken($id, $token) {
    $sql = "UPDATE us SET ban_token = ? WHERE id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$token, $id]);
}


public function getFilteredUsers($search = '', $role = '', $sortBy = 'id', $order = 'ASC') {
    $allowedSort = ['id', 'email', 'role', 'created_at'];
    $allowedOrder = ['ASC', 'DESC'];

    $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'id';
    $order = in_array(strtoupper($order), $allowedOrder) ? strtoupper($order) : 'ASC';

    $sql = "SELECT * FROM us WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (email LIKE :searchEmail OR name LIKE :searchName)";
        $params[':searchEmail'] = "%$search%";
        $params[':searchName'] = "%$search%";
    }

    if (!empty($role)) {
        $sql .= " AND role = :role";
        $params[':role'] = $role;
    }

    $sql .= " ORDER BY $sortBy $order";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
    public function storeVerificationToken($email, $token) {
        try {
            $sql = "UPDATE us SET verification_token = :token WHERE email = :email";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['token' => $token, 'email' => $email]);
        } catch(PDOException $e) {
            error_log("Erreur storeVerificationToken: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si le token de vérification existe
     */
    public function verifyEmailToken($token) {
        try {
            $sql = "SELECT email FROM us WHERE verification_token = :token LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['token' => $token]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? true : false;
        } catch(PDOException $e) {
            error_log("Erreur verifyEmailToken: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marquer l'email comme vérifié
     */
    public function markEmailAsVerified($token) {
        try {
            $sql = "UPDATE us SET email_verified = 1, verification_token = NULL WHERE verification_token = :token";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['token' => $token]);
        } catch(PDOException $e) {
            error_log("Erreur markEmailAsVerified: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si l'email est vérifié
     */
    public function isEmailVerified($email) {
        try {
            $sql = "SELECT email_verified FROM us WHERE email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result && $result['email_verified'] == 1;
        } catch(PDOException $e) {
            error_log("Erreur isEmailVerified: " . $e->getMessage());
            return false;
        }
    }




}
