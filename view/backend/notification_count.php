<?php
session_start();
require_once "../../Controller/NotificationController.php";

$recipient = $_SESSION['email'] ?? null; // ou identifiant utilisateur
if (!$recipient) {
    echo json_encode(['count' => 0]);
    exit;
}

$notifController = new NotificationController();
$count = $notifController->countUnread($recipient);

echo json_encode(['count' => $count]);
