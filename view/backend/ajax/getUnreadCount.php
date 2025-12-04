<?php
session_start();
require_once "../../Controller/NotificationController.php";

if (!isset($_SESSION['email'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$notifCtrl = new NotificationController();
$count = $notifCtrl->countUnread($_SESSION['email']);

echo json_encode(['count' => $count]);
