<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Vous devez être connecté !');</script>";
    header("Location: ../login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil Freelancer</title>
     <link rel="shortcut icon" type="image/x-icon" href="../img/logo.png?v=<?php echo time(); ?>">
     <link rel="stylesheet" href="../css/accueil_freelancer.css">

     <?php
$img = "../../uploads/profiles/" . ($_SESSION['user']['image'] ?? "rass.jpg");
?>
<?php

// Charger le contrôleur
require_once '../../../controller/notificationC.php';


$notifC = new NotificationController();
$nb_notif = $notifC->countUnread($_SESSION['id_user']);
$liste_notif = $notifC->getNotifications($_SESSION['id_user']);

// ID du freelancer connecté
$id_user = $_SESSION['user']['id_utilisateur'];
echo "<script>console.log('ID SESSION = $id_user');</script>";

// Compter les notifications non lues
$nb_notif = $notifC->countUnread($id_user);
echo "<script>console.log('NB NOTIF = $nb_notif');</script>";

?>



</head>

<body>

<!-- HEADER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">

<div class="header">

    <!-- TITRE -->
      <img src="../img/logo.png" class="immg">

    <!-- MENU BOOTSTRAP STYLE -->
    <div class="header-menu" style="margin-left:auto; margin-right:auto;">
    <ul class="nav">
        <li class="nav-item"><a href="#" class="nav-link active">Home</a></li>
        <li class="nav-item"><a href="blog.php" class="nav-link">Blogs</a></li>
        <li class="nav-item"><a href="#" class="nav-link">Reclamation</a></li>
        <li class="nav-item"><a href="projet_freelancer.php" class="nav-link">Projets</a></li>
       </ul>
</div>

<!-- 🔔 Notifications -->
<!-- ========== NOTIFICATION ICON + DROPDOWN ========== -->
<div class="notif-container">

    <!-- Icône + Badge -->
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
            <?php else: ?>
                <div class="notif-item empty">Aucune notification</div>
            <?php endif; ?>
        </div>
    </div>
</div>


    <!-- ⭐ Favoris -->
    <div class="icon-btn fav-btn">
        <i class="bi bi-heart"></i>
    </div>

    <!-- PROFILE DROPDOWN -->
    <div class="profile-dropdown">
        <img src="<?php echo $img; ?>" class="profile-img" alt="Profil">

        <div class="dropdown-menu">

            <a href="../profile.php" class="dropdown-item">
                <i class="bi bi-person-circle"></i>
                Mon Profil
            </a>

            <div class="dropdown-separator"></div>

            <div id="themeToggle" class="dropdown-item theme-switch">
                <i id="themeIcon" class="bi bi-moon-stars"></i>
                <span id="themeText">Mode Sombre</span>
                <div class="switch"></div>
            </div>

            <div class="dropdown-separator"></div>

            <a href="logout.php" class="dropdown-item">
                <i class="bi bi-box-arrow-right"></i>
                Déconnexion
            </a>

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

// Toggle du menu
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
