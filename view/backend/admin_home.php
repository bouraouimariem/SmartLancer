<?php
require_once "../../Controller/NotificationController.php";
$notifCtrl = new NotificationController();
$unreadCount = $notifCtrl->countUnread('admin');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #1e3c72, #4facfe);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* ----- Notification ----- */
        .notification-icon {
            position: fixed;
            top: 22px;
            right: 25px;
            font-size: 32px;
            z-index: 200;
        }

        .notification-icon a {
            text-decoration: none;
            color: #fff;
            position: relative;
            display: inline-block;
            transition: transform 0.2s ease;
        }

        .notification-icon a:hover {
            transform: scale(1.15);
        }

        .notification-icon .badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ff3b30;
            color: #fff;
            border-radius: 50%;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: 700;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.25);
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.25); }
            100% { transform: scale(1); }
        }

        /* ----- Container ----- */
        .container {
            background: #ffffffee;
            margin: auto;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
            padding: 60px 45px;
            width: 430px;
            text-align: center;
            animation: slideDown 0.9s ease forwards;
        }

        @keyframes slideDown {
            0% { opacity: 0; transform: translateY(-40px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .logo {
            width: 180px;
            margin-bottom: 20px;
        }

        /* ----- Buttons ----- */
        .btn-box {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin: 30px 0;
            align-items: center;
        }

        .admin-btn {
            padding: 18px 0;
            width: 80%;
            border-radius: 12px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            font-size: 17px;
            background: linear-gradient(135deg, #1e3c72, #4facfe);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            transition: all 0.35s ease;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .admin-btn:hover {
            transform: translateY(-4px) scale(1.04);
            box-shadow: 0 12px 35px rgba(0,0,0,0.3);
        }

        .admin-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.18);
            transform: skewX(-25deg);
            transition: 0.5s;
        }

        .admin-btn:hover::before {
            left: 200%;
        }

        /* ----- Footer ----- */
        footer {
            text-align: center;
            padding: 15px 0;
            color: #fff;
            font-size: 14px;
            opacity: 0.9;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<div class="notification-icon">
    <a href="notifications.php">
        <span class="alarm">&#128276;</span>
        <span class="badge" id="notifCount"><?= $unreadCount ?></span>
    </a>
</div>

<div class="container">

    <img src="css/logo.png" alt="Logo SmartLancer" class="logo">

    <h1>Bienvenue Administrateur</h1>

    <div class="btn-box">
        <a href="listReclamations.php" class="admin-btn">Réclamations</a>
        <a href="listReponses.php" class="admin-btn">Réponses</a>
    </div>

</div>

<footer>
    &copy; <?= date('Y') ?> SmartLancer | Administration
</footer>

<script>
function updateNotifications() {
    fetch('ajax/getUnreadCount.php')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('notifCount');
            badge.textContent = data.count;
            badge.style.display = data.count > 0 ? 'inline-block' : 'none';
        })
        .catch(err => console.error(err));
}

setInterval(updateNotifications, 5000);
</script>

</body>
</html>
