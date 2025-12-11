<?php
// démarre la session si elle n'existe pas déjà
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// récupère proprement le nom de l'utilisateur (fallback si absent)
$adminName = isset($_SESSION['user']['nom']) ? $_SESSION['user']['nom'] : 'Administrateur';
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>SmartLancer</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="../assets/vendors/select2/select2.min.css">
  <link rel="stylesheet" href="../assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="../assets/images/logo.png" />
</head>

<body>
  <div class="container-scroller">
    <!-- partial:../partials/_navbar.php -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
 <nav class="custom-header">
    
    <!-- GAUCHE : Logo + Nom -->
    <div class="header-left">
        <a class="navbar-brand" href="../index.php">
            <img src="../assets/images/logo.png" alt="logo">
        </a>
        <h1 class="brand-title">SmartLancer</h1>
    </div>

    <!-- DROITE : Notifications + Profil + Menu -->
    <div class="header-right">

        <!-- Bouton Notification -->
        <div class="notif-btn">
            <i class="fa-regular fa-bell"></i>
            <span class="notif-dot"></span>
        </div>

        <!-- Menu Profil -->
        <div class="profile-menu">
            <a class="profile-label" href="#" id="profileToggle">
                <img src="../assets/images/faces/face5.jpg" class="profile-img">
<span class="profile-name"><?= htmlspecialchars($adminName); ?></span>
                <i class="fa-solid fa-chevron-down arrow"></i>
            </a>

            <div class="profile-dropdown">
                <a href="#">Paramètres</a>
                <hr>
                <a href="../frontOffice/pages/logout.php">Déconnexion</a>
            </div>
        </div>

        <!-- Bouton menu -->
        <div class="more-btn">
            <i class="fa-solid fa-grip"></i>
        </div>
    </div>

</nav>

</nav>
<div class="container-fluid page-body-wrapper">      
      <!-- partial:../partials/_sidebar.php -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="../index.php">
              <i class="mdi mdi-home menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>    
          <li class="nav-item">
            <a class="nav-link" href="../pages/gestion_utilisateurs.php">
              <i class="mdi mdi-heart menu-icon"></i>
              <span class="menu-title">Gestion des Utilisateurs</span>
             <!---- <i class="menu-arrow"></i>-->
            </a>
          </li>    
          <li class="nav-item">
            <a class="nav-link" href="../pages/projets.php">
              <i class="mdi mdi-view-headline menu-icon"></i>
              <span class="menu-title">Gestion des Projets</span>
            </a>
          </li>    
          <li class="nav-item">
            <a class="nav-link" href="#">
              <i class="mdi mdi-chart-pie menu-icon"></i>
              <span class="menu-title">Gestion des Reclamations </span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../pages/blogs.php">
              <i class="mdi mdi-grid-large menu-icon"></i>
              <span class="menu-title">Gestion du Blog</span>
            </a>
          </li>    
          <li class="nav-item">
            <a class="nav-link" href="#">
              <i class="mdi mdi-emoticon menu-icon"></i>
              <span class="menu-title">Gestion des Commentaires</span>
            </a>
          </li>    
  </ul>
</nav>
<style>
* { box-sizing: border-box; margin:0; padding:0; }

.custom-header {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 25px;
    background: #fff;
    border-bottom: 1px solid #e5e5e5;
    position: fixed;
    top: 0;
    z-index: 999;
    height: 70px;
}
.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-left img {
    width: 50px;
}

.brand-title {
    font-family: 'Poppins', sans-serif;
    font-size: 22px;
    margin: 0;
}

/* DROITE */
.header-right {
    display: flex;
    align-items: center;
    gap: 25px;
}

/* Notifications */
.notif-btn {
    position: relative;
    cursor: pointer;
}

.notif-btn .fa-bell {
    font-size: 22px;
    color: #555;
}

.notif-dot {
    position: absolute;
    top: -3px;
    right: -3px;
    width: 8px;
    height: 8px;
    background: red;
    border-radius: 50%;
}

/* Profil */
.profile-menu { position: relative; }

.profile-label {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #222;
    cursor: pointer;
}

.profile-img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
}

.profile-dropdown {
      position: absolute;
      top: 55px;
      right: 0;
      width: 180px;
      background: white;
      border: 1px solid #ddd;
      padding: 10px 0;
      border-radius: 10px;
      display: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .profile-dropdown a {
      display: block;
      padding: 10px 15px;
      font-size: 14px;
      color: #333;
      text-decoration: none;
    }

.profile-menu.active .profile-dropdown {
    display: block;
}

.more-btn i {
    font-size: 20px;
    cursor: pointer;
    color: #555;
}

.profile-menu .profile-label {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      color: #333;
      text-decoration: none;
    }
</style>

<script>
document.getElementById("profileToggle").addEventListener("click", function(e) {
    e.preventDefault();
    document.querySelector(".profile-menu").classList.toggle("active");
});
</script>
