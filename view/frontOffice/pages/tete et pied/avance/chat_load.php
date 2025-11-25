<?php
session_start();
include '../../../../../controller/messageC.php';
$messageC = new messageController();

$id_room = $_GET['id_room'] ?? null;
$id_user = $_SESSION['id_user'] ?? 0;

if ($id_room) {
    $messages = $messageC->getMessagesByRoom($id_room) ?: [];
    foreach ($messages as $m) {
        $class = ($m['id_user'] == $id_user) ? 'sent' : 'received';
        echo '<div class="message ' . $class . '">';
        echo htmlspecialchars($m['message']) . '<br><small>' . $m['date_mes'] . '</small>';
        echo '</div>';
    }
}
?>
