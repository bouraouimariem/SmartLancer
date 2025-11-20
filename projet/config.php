<?php
class Config {
    private $host = "localhost";
    private $db_name = "reclamation"; // nom de ta base
    private $username = "root";
    private $password = "";

    public function getConnexion() {
        try {
            $conn = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->db_name,
                $this->username,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return null;
        }
    }
}
?>
