<?php 
include '../../../../../controller/propositionC.php'; 
include '../../../../../controller/publicationC.php'; 
include '../../../../../controller/roomC.php'; 

$roomC = new RoomController();

if (isset($_GET['id_propo'])) {
    $id_propo = $_GET['id_propo'];

    $propositionC = new propositionController();
    $proposition = $propositionC->get_proposition_by_id($id_propo);

    if ($proposition) {
        $id_pub = $proposition['id_pub'];
        $id_user2 = $proposition['id_user'];

        $publicationC = new publicationController();
        $publication = $publicationC->getPublicationById($id_pub);
        $id_user1 = $publication['id_user'];

        // ✅ Vérifie si une room existe déjà pour cette publication
        $existing_room = $roomC->getRoomByPublication($id_pub);

        if (!$existing_room) {
            // ✅ Crée une seule room par publication
            $date = new DateTime();
            $room = new Room(null, $id_pub, $id_propo, $id_user1, $id_user2, $date);
            $roomC->create_room($room);
        }

        // ✅ Met à jour les statuts
        $propositionC->modif_status_propo($id_propo);
        $publicationC->modif_status_pub($id_pub);
        $propositionC->change_propo_pubstatus($id_pub, 'refuse');

        header("Location: ../../projet_client.php");
        exit();
    } else {
        echo "Proposition non trouvée.";
    }
} else {
    echo "ID proposition manquant.";
}
?>
