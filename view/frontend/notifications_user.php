<?php
require_once __DIR__ . '/../../Model/NotificationUser.php';
$notif = new NotificationUser();

// Actions
if (isset($_GET['mark'])) {
    $notif->markAsRead($_GET['mark']);
    header("Location: notifications_user.php");
    exit;
}

if (isset($_GET['delete'])) {
    $notif->delete($_GET['delete']);
    header("Location: notifications_user.php");
    exit;
}

$notifications = $notif->getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Notifications</title>
<style>
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

<h2>📬 Vos notifications</h2>

<?php foreach ($notifications as $n): ?>
    <div class="notification <?= $n['is_read'] ? '' : 'unread' ?>">

        <div class="btn-row">
            <a class="open-btn" href="reclamations.php?open=<?= $n['id_reclamation'] ?>">
              Ouvrir la réclamation
             </a>
            <!-- bouton supprimer -->
            <a class="delete-btn" 
               href="notifications_user.php?delete=<?= $n['id_notification'] ?>"
               onclick="return confirm('Supprimer cette notification ?');">
               Supprimer
            </a>
        </div>

        <p><?= htmlspecialchars($n['message']) ?></p>
        <small><?= $n['date'] ?></small>

        <?php if (!$n['is_read']): ?>
            <br>
            <a class="mark-read" href="notifications_user.php?mark=<?= $n['id_notification'] ?>">
                Marquer comme lue
            </a>
        <?php endif; ?>

    </div>
<?php endforeach; ?>

<!-- bouton retour -->
<a class="back" href="landing.php">⬅ Retour à l'accueil</a>

</body>
</html>
