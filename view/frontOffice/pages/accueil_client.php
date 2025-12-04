<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Vous devez être connecté !');</script>";
    header("Location: ../login.php");
    exit();
}

require_once '../../../controller/notificationC.php';

$notifC = new NotificationController();

// ✅ ✅ ✅ UN SEUL ID OFFICIEL
$id_user = $_SESSION['user']['id_utilisateur'];

// ✅ ✅ ✅ Notifications DU BON UTILISATEUR SEULEMENT
$nb_notif = $notifC->countUnread($id_user);
$liste_notif = $notifC->getNotifications($id_user);

$img = "../../uploads/profiles/" . ($_SESSION['user']['image'] ?? "rass.jpg");
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil Client</title>
    <link rel="shortcut icon" type="image/x-icon" href="../img/logo.png?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/accueil_client.css">


</head>

<body>

<div class="header">

    <!-- LOGO -->
    <img src="../img/logo.png" class="immg">

    <!-- MENU CLIENT -->
    <div class="header-menu">
        <ul class="nav">
            <li><a href="#" class="nav-link active">Home</a></li>
            <li><a href="#" class="nav-link">Freelancers</a></li>
             <li><a href="blog.php" class="nav-link">Blogs</a></li>
            <li><a href="projet_client.php" class="nav-link">Mes Projets</a></li>
            <li><a href="#" class="nav-link">Reclamation</a></li>
        </ul>
    </div>

    <!-- ICONES NOTIFICATION & FAVORIS -->
<div class="notif-container">

    <!-- Notifications -->
    <div class="icon-btn notif-btn" onclick="toggleNotif()">
        <i class="bi bi-bell"></i>

        <?php if ($nb_notif > 0): ?>
            <span class="notif-count"><?= $nb_notif ?></span>
        <?php endif; ?>
    </div>

    <!-- Dropdown -->
    <div id="notif-dropdown" class="notif-dropdown">
        <div class="notif-header">
            <strong>Notifications (<?= $nb_notif ?>)</strong>
        </div>

        <div class="notif-list">
    <?php if (!empty($liste_notif)): ?>
        <?php foreach ($liste_notif as $n): ?>
            <div class="notif-item">
                <h4><?= $n['titre'] ?></h4>

                <p>
                    <?= $n['message'] ?><br>
                    <strong style="color:#2c8f4c;">
                        Projet : <?= $n['nom_pub'] ?? 'Projet supprimé' ?>
                    </strong>
                </p>

                <span class="notif-date"><?= $n['date_notif'] ?></span>
            </div>
        <?php endforeach; ?> 
    <?php endif; ?>          
</div>
</div>
</div>


     <!-- ⭐ Favoris -->
    <div class="icon-btn fav-btn">
        <i class="bi bi-heart"></i>
    </div>





    <!-- PROFILE -->
    <div class="profile-dropdown">
        <img src="<?php echo $img; ?>" class="profile-img">

        <div class="dropdown-menu">

            <a href="../profile.php" class="dropdown-item">
                <i class="bi bi-person-circle"></i> Mon Profil
            </a>

            <div class="dropdown-separator"></div>

            <div id="themeToggle" class="dropdown-item">
                <i id="themeIcon" class="bi bi-moon-stars"></i>
                <span id="themeText">Mode Sombre</span>
            </div>

            <div class="dropdown-separator"></div>

            <a href="logout.php" class="dropdown-item">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </a>

        </div>
    </div>

</div>

<script>

    function toggleNotif() {
    const drop = document.getElementById("notif-dropdown");
    drop.style.display = (drop.style.display === "block") ? "none" : "block";
}

// Ferme si clique en dehors
document.addEventListener("click", function (e) {
    const container = document.querySelector(".notif-container");
    if (!container.contains(e.target)) {
        document.getElementById("notif-dropdown").style.display = "none";
    }
});

// Ouvrir/fermer dropdown
document.querySelector(".profile-img").onclick = function() {
    const menu = document.querySelector(".dropdown-menu");
    menu.style.display = menu.style.display === "flex" ? "none" : "flex";
};

// fermer si clic extérieur
document.addEventListener("click", (e) => {
    if (!e.target.closest(".profile-dropdown")) {
        document.querySelector(".dropdown-menu").style.display = "none";
    }
});

/* ---------------------------
   MODE SOMBRE / CLAIR
----------------------------*/
const themeToggle = document.getElementById("themeToggle");
const themeText = document.getElementById("themeText");
const themeIcon = document.getElementById("themeIcon");

let savedTheme = localStorage.getItem("theme") || "light";
if (savedTheme === "dark") {
    document.body.classList.add("dark");
    themeText.textContent = "Mode Sombre";
    themeIcon.className = "bi bi-moon-stars";
}

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
