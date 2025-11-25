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
     <?php
$img = "../../uploads/profiles/" . ($_SESSION['user']['image'] ?? "rass.jpg");
?>


    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #e0f5e8ff;
        }

        /* HEADER */
        .header {
    background: #2c8f4c;
    height: 70px;              /* 👉 hauteur FIXE */
    padding: 0 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
     overflow: visible; /* IMPORTANT */
    }


        

        .logout-btn {
            background: #d9534f;
            padding: 8px 15px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        /* CONTAINER */
        .container {
            width: 80%;
            margin: 50px auto;
            text-align: center;
        }

        .btn-menu {
            display: inline-block;
            width: 260px;
            padding: 20px;
            margin: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #333;
            font-size: 20px;
            transition: 0.3s;
        }

        .btn-menu:hover {
            transform: scale(1.05);
            background: #2c8f4c;
            color: white;
        }

      /* --- PROFILE DROPDOWN MODERNE --- */
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
    width: 260px;
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

</div>



<script>
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
