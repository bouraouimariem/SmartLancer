<?php
class Config {

    private $host = "localhost";
    private $db_name = "reclamation";
    private $username = "root";
    private $password = "";
    
    public function getConnexion() {
        try {
            $conn = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->db_name.";charset=utf8",
                $this->username,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;

        } catch(PDOException $e) {
            die("Erreur connexion : " . $e->getMessage());
        }
    }
}
?>
