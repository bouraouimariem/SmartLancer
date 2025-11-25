<?php
include_once(__DIR__ . '/../config.php');
include(__DIR__ . '/../model/room.php');
class RoomController
{
    public function create_room($room)
    {
        $sql = "INSERT INTO room (id_pub, id_propo, id_user1, id_user2, date_debut) 
        VALUES 
        ( :id_pub, :id_propo, :id_user1, :id_user2, :date_debut)";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id_pub' => $room->getIdPub(),
                'id_propo' => $room->getIdPropo(),
                'id_user1' => $room->getIdUser1(),
                'id_user2' => $room->getIdUser2(),
                'date_debut' => $room->getDateRoom()->format('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function getLastRoomId()
{
    $db = config::getConnexion();
    $sql = "SELECT id_room FROM room ORDER BY id_room DESC LIMIT 1";
    try {
        $stmt = $db->query($sql);
        $row = $stmt->fetch();
        return $row ? $row['id_room'] : null;
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
        return null;
    }
}

    public function get_num_rooms_for_user($id_user)
    {
        $sql = 'SELECT COUNT(*) FROM room WHERE id_user1=:id_user OR id_user2=:id_user ';
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_user', $id_user);
            $stmt->execute();
            return $stmt->fetchColumn();
        }catch (Exception $e) {
            echo 'ERROR: '. $e->getMessage();
        }
    }

    public function getRoomsByUser($id_user)
{
    $sql = 'SELECT * FROM room WHERE id_user1 = :id_user OR id_user2 = :id_user ORDER BY date_debut DESC';
    $db = config::getConnexion();
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo 'Erreur: ' . $e->getMessage();
        return [];
    }
}

public function getRoomByUsersAndPublication($id_user1, $id_user2, $id_pub)
{
    $sql = "SELECT * FROM room 
            WHERE id_pub = :id_pub 
            AND (
                (id_user1 = :id_user1 AND id_user2 = :id_user2)
                OR (id_user1 = :id_user2 AND id_user2 = :id_user1)
            )";
    $db = config::getConnexion();
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'id_pub' => $id_pub,
            'id_user1' => $id_user1,
            'id_user2' => $id_user2
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo 'Erreur : ' . $e->getMessage();
        return null;
    }
}

public function getRoomByPublication($id_pub)
{
    $sql = 'SELECT * FROM room WHERE id_pub = :id_pub LIMIT 1';
    $db = config::getConnexion();
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id_pub', $id_pub, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo 'Erreur: ' . $e->getMessage();
        return null;
    }
}


}


?>