<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/message.php');

class messageController
{
    public function add_message($mess)
    {
        $sql = 'INSERT INTO messages (id_room, id_user, message, date_mes) 
                VALUES (:id_room, :id_user, :message, :date_mes)';
        $db = config::getConnexion();

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_room' => $mess->getRoomId(),
                ':id_user' => $mess->getUserId(), // ✅ Correction ici
                ':message' => $mess->getMessage(),
                ':date_mes' => $mess->getDateMes()->format('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            echo '❌ Error: ' . $e->getMessage();
        }
    }

    public function getMessagesByRoom($id_room)
    {
        $sql = 'SELECT * FROM messages WHERE id_room = :id_room ORDER BY date_mes ASC';
        $db = config::getConnexion();

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_room', $id_room, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo '❌ Error: ' . $e->getMessage();
            return [];
        }
    }
}
?>
