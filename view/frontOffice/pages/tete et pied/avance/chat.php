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
     <?php
$img = "../../../../uploads/profiles/" . ($_SESSION['user']['image'] ?? "rass.jpg");
?>

    <style>
        /* === HEADER ECO === */
         .header {
    background: #2c8f4c;
    height: 70px;              /* 👉 hauteur FIXE */
    padding: 0 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
     overflow: visible; /* IMPORTANT */
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


        .profile-dropdown {
    position: relative;
    display: inline-block;
}

.profile-img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid #fff;
    object-fit: cover;
    transition: 0.3s;
}

.profile-img:hover { transform: scale(1.1); }

/* STYLE CARTE TRANSLUCIDE */
.dropdown-menu {
    position: absolute;
    right: 0;
    top: 60px;
    width: 220px;
    padding: 20px;
    border-radius: 20px;
    background: #2c8f4b79;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow: #2c8f4c;

    display: none;
    flex-direction: column;
    animation: fadeIn 0.2s ease;
}

/* Animation ouverture */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.dropdown-item {
    display: flex;
    align-items: center;
    padding: 12px 10px;
    gap: 12px;
    border-radius: 10px;
    text-decoration: none;
    color: white;
    font-size: 16px;
    transition: 0.2s;
}

.dropdown-item:hover {
    background: #2c8f4c;
}

/* Icones dans le dropdown */
.dropdown-item i {
    font-size: 20px;
}

/* Séparateur */
.dropdown-separator {
    height: 2px;
    background: rgba(0, 83, 28, 0.3);
    margin: 10px 0;
}


.immg {
    height: 140%;              /* 👉 l’image occupe toute la hauteur du header */
    width: auto;               /* 👉 garde proportions */
    object-fit: contain; 
    margin-left: -29px;      /* 👉 pas de déformation */
    margin-bottom:8px;
}

/* --- TOGGLE SWITCH --- */
.theme-switch {
    display: flex;
    align-items: center;
    cursor: pointer;
    gap: 12px;
    color: white;
    font-size: 16px;
}

.switch {
    position: relative;
    width: 50px;
    height: 24px;
    background: rgba(255,255,255,0.3);
    border-radius: 50px;
    transition: 0.3s;
}

.switch::after {
    content: "";
    position: absolute;
    width: 22px;
    height: 22px;
    background: white;
    border-radius: 50%;
    top: 1px;
    left: 1px;
    transition: 0.3s;
}

/* Quand dark mode activé */
body.dark .switch {
    background: #111;
}

body.dark .switch::after {
    transform: translateX(26px);
}

/* Mode sombre global */
body.dark {
    background: #1e3b2f ;
    color: white;
}

body.dark .btn-menu {
    background: #1f1f1f;
    color: white;
}

body.dark .header {
    background: #1b5b32;
}

body.dark .dropdown-menu {
    background: #1b5b3299;
}

body.dark .chat-box
{
    background: #26493a;
    color: white ;
}

body.dark input[type="text"]
{
    background: #285C47;
    color: white ;
    border:1px solid #4caf50;
}

 body.dark .sent
{
    background: #09680cff;
    margin-left: auto;
}


/* --- MENU NAVIGATION DANS LE HEADER --- */
.nav {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}


.header-menu {
    display: flex;
    justify-content: center;
    align-items: center;
    
}

.header-menu .nav {
    display: flex;
    gap: 18px;
}

.header-menu .nav-link {
    color: white !important;
    font-size: 16px;
    padding: 8px 14px;
    border-radius: 8px;
    transition: 0.3s;
    text-decoration:none;
}

.header-menu .nav-link:hover {
    background: rgba(255, 255, 255, 0.25);
}

.header-menu .nav-link.active {
    background: white;
    color: #2c8f4c !important;
    font-weight: bold;
}

/* MODE SOMBRE */
body.dark .header-menu .nav-link {
    color: #fff !important;
}

body.dark .header-menu .nav-link.active {
    background: #fff;
    color: #1b5b32 !important;
}

body.dark .header-menu .nav-link:hover {
    background: rgba(255,255,255,0.25);
}



    </style>
</head>

<body>

<!-- ================= HEADER ================= -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">

<div class="header">

    <!-- TITRE -->
      <img src="../../../img/logo.png" class="immg">

    <!-- PROFILE DROPDOWN -->
    <div class="profile-dropdown">
        <img src="<?php echo $img; ?>" class="profile-img" alt="Profil">

        <div class="dropdown-menu">

            <div id="themeToggle" class="dropdown-item theme-switch">
                <i id="themeIcon" class="bi bi-moon-stars"></i>
                <span id="themeText">Mode Sombre</span>
                <div class="switch"></div>
            </div>

            <div class="dropdown-separator"></div>

            <a href="../../logout.php" class="dropdown-item">
                <i class="bi bi-box-arrow-right"></i>
                Déconnexion
            </a>

        </div>
    </div>

</div>


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


<script>
// Auto refresh chat
setInterval(() => {
    fetch("chat_load.php?id_room=<?= $id_room ?>")
        .then(r => r.text())
        .then(html => {
            document.getElementById('chatBox').innerHTML = html;
        });
}, 3000);
document.querySelector(".profile-img").onclick = function() {
    const menu = document.querySelector(".dropdown-menu");
    menu.style.display = menu.style.display === "flex" ? "none" : "flex";
};

// Clic extérieur → fermer
document.addEventListener("click", function(e) {
    if (!e.target.closest(".profile-dropdown")) {
        document.querySelector(".dropdown-menu").style.display = "none";
    }
});


/* -------------------------------------------------
   THÈME SOMBRE / CLAIR AVEC SAUVEGARDE LOCALSTORAGE
---------------------------------------------------*/
const themeToggle = document.getElementById("themeToggle");
const themeText   = document.getElementById("themeText");
const themeIcon   = document.getElementById("themeIcon");

// Lire la valeur enregistrée
let savedTheme = localStorage.getItem("theme");

// Par défaut → thème clair
if (!savedTheme) {
    localStorage.setItem("theme", "light");
    savedTheme = "light";
}

// Appliquer le thème sauvegardé
if (savedTheme === "dark") {
    document.body.classList.add("dark");
    themeText.textContent = "Mode Sombre";
    themeIcon.className = "bi bi-moon-stars";
} else {
    document.body.classList.remove("dark");
    themeText.textContent = "Mode Claire";
    themeIcon.className = "bi bi-sun";
}

// Toggle thème au clic
themeToggle.onclick = function () {
    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {
        themeText.textContent = "Mode Sombre";
        themeIcon.className = "bi bi-moon-stars";
        localStorage.setItem("theme", "dark");
    } else {
        themeText.textContent = "Mode Claire";
        themeIcon.className = "bi bi-sun";
        localStorage.setItem("theme", "light");
    }
};
</script>

</body>
</html>
