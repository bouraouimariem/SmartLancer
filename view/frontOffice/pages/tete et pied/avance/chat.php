<?php
session_start();
include '../../../../../controller/messageC.php';
include '../../../../../controller/roomC.php';

// Vérifier connexion
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Vous devez être connecté pour discuter.'); window.location.href='../login.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];
$id_room = $_GET['id_room'] ?? null;

if (!$id_room) {
    echo "Room non spécifiée.";
    exit();
}

$messageC = new messageController();

// === ENVOI MESSAGE ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['message'])) {
    $msg_text = trim($_POST['message']);

    if ($msg_text !== '') {
        $msg = new Messages(
            null,
            (int)$id_room,
            (int)$id_user,
            $msg_text,
            new DateTime()
        );
        $messageC->add_message($msg);
    }

    header("Location: chat.php?id_room=" . $id_room);
    exit();
}

// === CHARGER LES MESSAGES ===
$messages = $messageC->getMessagesByRoom($id_room);
if (!is_array($messages)) $messages = [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Discussion</title>

    <style>
        /* === HEADER ECO === */
        .green-header {
            background: linear-gradient(90deg, #1b5e20, #4caf50);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        .logo-zone { display: flex; align-items: center; gap: 10px; }
        .earth-icon { width: 40px; height: 40px; }

 

        /* === CHAT BOX === */
        body { font-family: Arial, sans-serif; background: #edf7f1; margin: 0; }

        .chat-container {
            width: 60%;
            margin: 40px auto;
        }
        .chat-box {
            background: white;
            height: 65vh;
            border-radius: 12px;
            padding: 20px;
            overflow-y: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .message {
            margin: 10px 0; padding: 12px;
            border-radius: 10px; width: fit-content;
            max-width: 70%;
        }
        .sent {
            background: #c8e6c9;
            margin-left: auto;
        }
        .received {
            background: #f1f8e9;
        }

        /* === FORM === */
        form {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        input[type="text"] {
            flex: 1; padding: 12px;
            border: 1px solid #8bc34a;
            border-radius: 8px;
        }
        button {
            background: #2e7d32; color: white;
            border: none; padding: 12px 20px;
            border-radius: 8px; cursor: pointer;
        }
        button:hover { background: #1b5e20; }

        /* === FOOTER ECO === */
        footer {
            background: #1b5e20;
            color: #c8e6c9;
            text-align: center;
            padding: 15px;
            margin-top: 20px;
        }

        /* === BOUTON SWITCH === */
.toggle-btn {
    background: #2e7d32;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
}
.toggle-btn:hover {
    background: #1b5e20;
}

/* === MODE SOMBRE (COULEUR : #1e3b2f) === */
.dark-mode {
    background: #1e3b2f !important;
    color: white !important;
}

.dark-mode .chat-box {
    background: #26493a;
    color: white;
}

.dark-mode .sent {
    background: #1b5e20;
    color: white;
}

.dark-mode .received {
    background: #285c47;
    color: white;
}

.dark-mode input[type="text"] {
    background: #285c47;
    color: white;
    border: 1px solid #4caf50;
}

.dark-mode footer {
    background: #0d241c;
    color: #c8e6c9;
}

.dark-mode .green-header {
    background: #0f281f;
    color: white;
}

    </style>
</head>

<body>

<!-- ================= HEADER ================= -->
<header class="green-header">
    <div class="logo-zone">
        <img src="../../../img/logo.png" class="earth-icon">
        <h2>SmartLancer</h2>
    </div>

    <!-- 🔘 Bouton Dark/Light -->
    <button id="themeToggle" class="toggle-btn">🌙</button>
</header>


<!-- ================= CHAT ================= -->
<div class="chat-container">
    <div class="chat-box" id="chatBox">
        <?php foreach ($messages as $m): ?>
            <div class="message <?= ($m['id_user'] == $id_user) ? 'sent' : 'received' ?>">
                <?= htmlspecialchars($m['message']) ?><br>
                <small><?= $m['date_mes'] ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST">
        <input type="text" name="message" placeholder="Écrire un message..." required>
        <button type="submit">Envoyer</button>
    </form>
</div>

<!-- ================= FOOTER ================= -->
<footer>
    🌿 Engagés pour les Objectifs de Développement Durable | EcoLancer 2025
</footer>

<script>
// Auto refresh chat
setInterval(() => {
    fetch("chat_load.php?id_room=<?= $id_room ?>")
        .then(r => r.text())
        .then(html => {
            document.getElementById('chatBox').innerHTML = html;
        });
}, 3000);
const btn = document.getElementById("themeToggle");
const body = document.body;

// Charger thème sauvegardé
if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark-mode");
    btn.textContent = "☀️";
}

btn.onclick = () => {
    body.classList.toggle("dark-mode");

    if (body.classList.contains("dark-mode")) {
        localStorage.setItem("theme", "dark");
        btn.textContent = "☀️";
    } else {
        localStorage.setItem("theme", "light");
        btn.textContent = "🌙";
    }
};
</script>

</body>
</html>
