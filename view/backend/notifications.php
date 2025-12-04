<?php
session_start();

// Pour test temporaire (à retirer en prod)
if(!isset($_SESSION['email'])) {
    $_SESSION['email'] = 'admin';
}

require_once "../../Controller/NotificationController.php";
$notifCtrl = new NotificationController();
$recipient = $_SESSION['email'];

// 🔥 SUPPRESSION D'UNE NOTIFICATION
if (isset($_GET['delete'])) {
    $notifCtrl->delete((int)$_GET['delete']);
    header("Location: notifications.php");
    exit;
}

// Marquer comme lue
if(isset($_GET['mark_read'])) {
    $notifCtrl->markAsRead((int)$_GET['mark_read']);
    header("Location: notifications.php");
    exit;
}

// Récupérer les notifications
$notifications = $notifCtrl->getAll($recipient);
$unreadCount = $notifCtrl->countUnread($recipient);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Notifications Admin</title>

<style>
body {
    font-family: "Inter", sans-serif;
    background: #f3f7ff;
    margin: 0;
    padding: 30px;
}

h1 {
    margin-bottom: 25px;
    color: #1e3c72;
}

/* ===== CARD NOTIFICATION ===== */
.notification {
    background: #ffffff;
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 12px;
    position: relative;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    border-left: 6px solid #c5d0e6;
    transition: 0.2s ease;
}

.notification.unread {
    border-left: 6px solid #ff3b30;
    background: #fff4f4;
}

.btn-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.open-btn, .delete-btn {
    text-decoration: none;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.open-btn { background: #1e3c72; }
.open-btn:hover { background: #163460; }
.open-btn::before { content: "📂"; }

.delete-btn { background: #e63946; }
.delete-btn:hover { background: #c62836; }
.delete-btn::before { content: "🗑️"; }

.mark-read {
    margin-top: 10px;
    font-size: 13px;
    color: #1e3c72;
    text-decoration: underline;
}

.back {
    display: inline-block;
    margin-top: 25px;
    color: #1e3c72;
    font-weight: 600;
    text-decoration: none;
}
</style>
</head>

<body>

<h1>Notifications (<?= $unreadCount ?> non lues)</h1>

<?php if(empty($notifications)): ?>
    <p>Aucune notification.</p>
<?php else: ?>

    <?php foreach($notifications as $notif): ?>
        <div class="notification <?= $notif['is_read'] == 0 ? 'unread' : '' ?>">

            <div class="btn-row">
                <a href="listReclamations.php" class="open-btn">Ouvrir</a>

                <!-- 🔥 Bouton Supprimer -->
                <a href="?delete=<?= $notif['id_notification'] ?>" 
                   class="delete-btn"
                   onclick="return confirm('Supprimer cette notification ?');">
                    Supprimer
                </a>
            </div>

            <p><?= htmlspecialchars($notif['message']) ?></p>
            <small><?= $notif['date'] ?></small>

            <?php if($notif['is_read'] == 0): ?>
                <a href="?mark_read=<?= $notif['id_notification'] ?>" class="mark-read">Marquer comme lue</a>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>

<?php endif; ?>

<a href="admin_home.php" class="back">← Retour à l'accueil</a>

</body>
</html>
