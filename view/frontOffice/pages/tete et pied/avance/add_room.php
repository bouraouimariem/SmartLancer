<?php 
require_once  '../../../../../controller/propositionC.php'; 
require_once  '../../../../../controller/publicationC.php'; 
require_once  '../../../../../controller/roomC.php'; 
require_once  '../../../../../controller/notificationC.php';   // Notifications
require_once  '../../../../../config.php'; // ✅ FICHIER DE CONNEXION DB
$db = config::getConnexion(); // ✅ récupération de la connexion PDO




$roomC = new RoomController();

if (!isset($_GET['id_propo'])) {
    die("❌ ID proposition manquant.");
}

$id_propo = $_GET['id_propo'];

$propositionC = new propositionController();
$publicationC = new publicationController();
$notificationC = new NotificationController();

/* ===============================
   1️⃣ Récupération de la proposition
   =============================== */
$proposition = $propositionC->get_proposition_by_id($id_propo);

if (!$proposition) {
    die("❌ Proposition non trouvée.");
}

$id_pub   = $proposition['id_pub'];
$id_user2 = $proposition['id_user']; // Freelancer

/* ===============================
   2️⃣ Récupérer la publication
   =============================== */
$publication = $publicationC->getPublicationById($id_pub);

if (!$publication) {
    die("❌ Publication non trouvée.");
}

$id_user1 = $publication['id_user']; // Client


/* ===============================
   3️⃣ Room : vérifier si déjà créée
   =============================== */
$existing_room = $roomC->getRoomByPublication($id_pub);

if (!$existing_room) {
    $date = new DateTime();
    $room = new Room(null, $id_pub, $id_propo, $id_user1, $id_user2, $date);
    $roomC->create_room($room);
}


/* ===============================
   4️⃣ Mise à jour des statuts
   =============================== */
$propositionC->modif_status_propo($id_propo);  // accepter cette proposition
$publicationC->modif_status_pub($id_pub);      // publication acceptée
$propositionC->change_propo_pubstatus($id_pub, 'refuse'); // refuser les autres


/* ===============================
   5️⃣ Notification → FREELANCER
   =============================== */

// ✅ Connexion DB déjà disponible avec $db

// ✅ Récupérer le nom réel de la publication
$sql = "SELECT nom_pub FROM publications WHERE id_pub = :id_pub";
$stmt = $db->prepare($sql);
$stmt->execute(['id_pub' => $id_pub]);
$pub = $stmt->fetch(PDO::FETCH_ASSOC);

$nom_pub = $pub['nom_pub']; 

// ✅ Message PRO avec nom réel
$message = "Votre proposition pour la publication \"$nom_pub\" a été acceptée !";

// ✅ Envoi notification (✅ 4 paramètres)
$notificationC->ajouterNotification(
    $id_user2,               // destinataire = freelancer
    $id_pub,                 // ✅ ID publication
    "Proposition acceptée",  // titre
    $message                 // message
);



/* ===============================
   6️⃣ Redirection
   =============================== */
header("Location: ../../projet_client.php");
exit();

?>
