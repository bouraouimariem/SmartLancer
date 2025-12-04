<?php

class NotificationController {

    private $conn;

    public function __construct() {
        // 🔥 Même style que ton messageController
        include_once(__DIR__ . '/../config.php');
        $this->conn = config::getConnexion();
    }

    // ➕ Ajouter une notification
  public function ajouterNotification($id_user, $id_pub, $titre, $message)
{
    $sql = "INSERT INTO notifications (id_user, id_pub, titre, message)
            VALUES (:id_user, :id_pub, :titre, :message)";
    try {
        $query = $this->conn->prepare($sql);
        $query->execute([
            ':id_user' => $id_user,
            ':id_pub'  => $id_pub,
            ':titre'   => $titre,
            ':message' => $message
        ]);
    } catch (Exception $e) {
        echo "❌ Erreur ajouterNotification : " . $e->getMessage();
    }
}


    // 📌 Récupérer toutes les notifications d’un utilisateur
public function getNotifications($id_user) 
{
    $sql = "
        SELECT n.id_notif, n.titre, n.message, n.est_lu, n.date_notif, 
               p.nom_pub
        FROM notifications n
        LEFT JOIN publications p ON n.id_pub = p.id_pub
        WHERE n.id_user = :id_user
        ORDER BY n.date_notif DESC
    ";

    try {
        $query = $this->conn->prepare($sql);
        $query->bindValue(':id_user', $id_user);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        echo "❌ Erreur getNotifications : " . $e->getMessage();
        return [];
    }
}



    // 🔢 Compter les notifications non lues
    public function countUnread($id_user) {
        $sql = "SELECT COUNT(*) AS total 
                FROM notifications 
                WHERE id_user = :id_user AND est_lu = 0";
        try {
            $query = $this->conn->prepare($sql);
            $query->bindValue(':id_user', $id_user);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;

        } catch (Exception $e) {
            echo "❌ Erreur countUnread : " . $e->getMessage();
            return 0;
        }
    }
}
?>
