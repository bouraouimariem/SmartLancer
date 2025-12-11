<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../frontOffice/login.php");
    exit();
}

// ----------------------
//  CONTROLLER PUBLICATION
// ----------------------
require_once '../../controller/publicationC.php';
$publicationC = new publicationController();

// Nombre total publications
$nbPublications = $publicationC->countPublications();

// 3 derniers projets
$lastProjects = $publicationC->getLastThreePublications();

// ----------------------
//  CONTROLLER UTILISATEUR
// ----------------------
require_once '../../controller/utilisateurC.php';
$uC = new UtilisateurController();

// derniers utilisateurs
$lastUsers = $uC->getLastUsers();

// count par rôle
$count_clients = $uC->countByRole("client");
$count_freelancers = $uC->countByRole("freelance");

$total_users = $count_clients + $count_freelancers;

// ----------------------
//  CONTROLLER PROPOSITION
// ----------------------
require_once '../../controller/propositionC.php';
$propoC = new PropositionController();

// Dernière proposition
$lastPropo = $propoC->getLastProposition(); // doit retourner un array ou null


// ----------------------
//  CONTROLLER COMMENTAIRES
// ----------------------
require_once '../../controller/commentaireC.php';
$comC = new CommentaireC();


// Dernier commentaire
$lastCom = $comC->getLastComment();


// ----------------------
//  DERNIÈRE PUBLICATION
// ----------------------
$lastPub = $publicationC->getLastPublication(); // ajoute cette fonction si elle n’existe pas


// ----------------------
//  DERNIER UTILISATEUR
// ----------------------
$lastUser = $uC->getLastUser(); // doit exister dans ton UtilisateurController

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>SmartLancer</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

  <!-- endinject -->
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="assets/images/logo.png" />
</head>
<body>
  <div class="container-scroller">
    <div class="row p-0 m-0 proBanner" id="proBanner">
      <div class="col-md-12 p-0 m-0">
        <div class="card-body card-body-padding px-3 d-flex align-items-center justify-content-between">
          <div class="ps-lg-1">
            <div class="d-flex align-items-center justify-content-between">
              <p class="mb-0 font-weight-medium me-3 buy-now-text">Free 24/7 customer support, updates, and more with this template!</p>
              <a href="https://www.bootstrapdash.com/product/majestic-admin-pro/?utm_source=navbar&utm_medium=productdemo&utm_campaign=getpro" target="_blank" class="btn me-2 buy-now-btn border-0">Buy Now</a>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <a href="https://www.bootstrapdash.com/product/majestic-admin-pro/"><i class="mdi mdi-home me-3 text-white"></i></a>
            <button id="bannerClose" class="btn border-0 p-0">
              <i class="mdi mdi-close text-white me-0"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- partial:partials/_navbar.php -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <nav class="custom-header">
    
    <!-- GAUCHE : Logo + Nom -->
    <div class="header-left">
        <a class="navbar-brand" href="index.php">
            <img src="assets/images/logo.png" alt="logo">
        </a>
        <h1 class="brand-title">SmartLancer</h1>
    </div>

    <!-- DROITE : Notifications + Profil + Menu -->
    <div class="header-right">

       

        <div class="profile-menu">
    <a class="profile-label" href="#" id="profileToggle">
                <img src="assets/images/profile.jpg" class="profile-img">
                
                <span class="profile-name"><?= htmlspecialchars($_SESSION['nom']); ?></span>
                <i class="fa-solid fa-chevron-down arrow"></i>
            </a>

            <div class="profile-dropdown">
                <a href="#">Paramètres</a>
                <hr>
                <a href="../frontOffice/pages/logout.php">Déconnexion</a>
            </div>
        </div>

        <div class="more-btn">
            <i class="fa-solid fa-grip"></i>
        </div>
    </div>

</nav>

  <!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Header sans JS</title>
  <style>
    * { box-sizing: border-box; margin:0; padding:0; }
    body { font-family: sans-serif; }
/* Container principal */
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

/* GAUCHE */
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
.profile-menu {
    position: relative;
}

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
    top: 50px;
    right: 0;
    width: 170px;
    background: white;
    border: 1px solid #ddd;
    padding: 10px 0;
    border-radius: 10px;
    display: none;
}
.profile-menu.active .profile-dropdown {
    display: block;
}



/* More btn */
.more-btn i {
    font-size: 20px;
    cursor: pointer;
    color: #555;
}

    .top-header {
      width: 100%;
      background: white;
      height: 70px;
      border-bottom: 1px solid #e5e5e5;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding: 0 30px;
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 25px;
    }

    .notif-btn {
      position: relative;
      color: #555;
    }
    .notif-btn .fa-bell {
      font-size: 20px;
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

    .profile-menu {
      position: relative;
    }
    .profile-menu .profile-label {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      color: #333;
      text-decoration: none;
    }
    .profile-menu .profile-img {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
    }
    .profile-menu .profile-name {
      font-size: 15px;
      font-weight: 500;
    }
    .profile-menu .arrow {
      font-size: 12px;
      color: #666;
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
  



    .more-btn .fa-grip {
      font-size: 20px;
      color: #555;
      cursor: pointer;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  

</body>
</html>

</nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">      
      <!-- partial:partials/_sidebar.php -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="index.php">
        <i class="mdi mdi-home menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>    
    <li class="nav-item">
      <a class="nav-link" href="pages/gestion_utilisateurs.php">
        <i class="mdi mdi-heart menu-icon"></i>
        <span class="menu-title">Gestion des Utilisateurs</span>
       <!---- <i class="menu-arrow"></i>-->
      </a>
    </li>    
    <li class="nav-item">
      <a class="nav-link" href="pages/projets.php">
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
      <a class="nav-link" href="pages/blogs.php">
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
      <!-- partial -->

      <!--debut dashboard-->
      <div class="main-panel">

  <div class="content-wrapper">

    <!-- SECTION : Bienvenue -->
    <div class="row">
      <div class="col-12 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <h2 class="fw-bold">Bienvenue <?= htmlspecialchars($_SESSION['nom']); ?></h2>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION : Statistiques -->
    <div class="row">

      <div class="col-md-3 grid-margin stretch-card">
        <div class="card bg-primary text-white">
          <div class="card-body">
            <h4 class="card-title">Utilisateurs</h4>
           <h2 class="fw-bold"><?php echo $total_users; ?></h2>
<p>
  Clients : <?php echo $count_clients; ?>  
  <br>
  Freelancers : <?php echo $count_freelancers; ?>
</p>

          </div>
        </div>
      </div>

      <div class="col-md-3 grid-margin stretch-card">
        <div class="card bg-success text-white">
          <div class="card-body">
            <h4 class="card-title">Projets</h4>
            <h2><?php echo $nbPublications; ?></h2>

          </div>
        </div>
      </div>

      <div class="col-md-3 grid-margin stretch-card">
        <div class="card bg-warning text-white">
          <div class="card-body">
            <h4 class="card-title">Commentaires</h4>
            <h2 class="fw-bold">230</h2>
            <p>+30 cette semaine</p>
          </div>
        </div>
      </div>

      <div class="col-md-3 grid-margin stretch-card">
        <div class="card bg-info text-white">
          <div class="card-body">
            <h4 class="card-title">Visites</h4>
            <h2 class="fw-bold">930</h2>
            <p>Aujourd’hui</p>
          </div>
        </div>
      </div>

    </div>

    <!-- SECTION : Graphiques & Activités -->
    <div class="row">

      <!-- Graphique -->
      <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">📈 Activité des Projets</h4>

            <img 
              src="https://quickchart.io/chart?c={
                type:'line',
                data:{labels:['Jan','Fev','Mar','Avr','Mai','Juin'],
                datasets:[{label:'Projets',data:[3,5,8,6,10,7]}]}
              }"
              style="width:100%; height:300px; border-radius:5px;">
          </div>
        </div>
      </div>

      <div class="col-md-4 grid-margin stretch-card">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title">⚡ Activités Récentes</h4>

      <ul class="list-group">
        
        <!-- Dernier projet -->
        <li class="list-group-item">
          ✔️ Dernière publication :
          <b><?= htmlspecialchars($lastPub['nom_pub'] ?? 'Aucune') ?></b><br>
          <small class="text-muted">
            <?= isset($lastPub['date_pub']) ? date("d M Y", strtotime($lastPub['date_pub'])) : '' ?>
          </small>
        </li>

        <!-- Dernier utilisateur -->
        <li class="list-group-item">
          👤 Dernier utilisateur :
          <b><?= htmlspecialchars($lastUser['nom'] ?? 'Aucun') ?></b><br>
          <small class="text-muted">
            <?= htmlspecialchars($lastUser['Email'] ?? '') ?>
          </small>
        </li>

        <!-- Dernier commentaire -->
        <li class="list-group-item">
          💬 Dernier commentaire :
          <b><?= htmlspecialchars($lastCom['namec'] ?? 'Aucun') ?></b><br>
          <small class="text-muted">
            <?= isset($lastCom['date_com']) ? date("d M Y", strtotime($lastCom['date_com'])) : '' ?>
          </small>
        </li>

        <!-- Dernière proposition -->
        <li class="list-group-item">
          📝 Dernière proposition :
          <?php if ($lastPropo): ?>
            Projet #<?= $lastPropo['id_pub'] ?> — 
            <b><?= htmlspecialchars($lastPropo['montant_propo']) ?> DT</b><br>
            <small class="text-muted">
              <?= date("d M Y", strtotime($lastPropo['date_propo'])) ?>
            </small>
          <?php else: ?>
            Aucune
          <?php endif; ?>
        </li>

      </ul>
    </div>
  </div>
</div>


    </div>

    <!-- SECTION : Derniers utilisateurs + Derniers projets -->
    <div class="row">

      <!-- Utilisateurs -->
      <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">👤 Derniers Utilisateurs</h4>

            <table class="table table-striped">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($lastUsers as $u): ?>
    <tr>
        <td><?= htmlspecialchars($u['nom']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td>
        <td><?= date("d M Y", strtotime($u['created_at'])); ?></td>
    </tr>
<?php endforeach; ?>
</tbody>

</table>


          </div>
        </div>
      </div>

      <!-- Projets -->
      <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">📝 Derniers Projets</h4>

            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Projet</th>
                  <th>Catégorie</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
<?php foreach ($lastProjects as $p) { ?>
    <tr>
        <!-- Nom du projet -->
        <td><?= htmlspecialchars($p['nom_pub']) ?></td>

        <!-- Catégorie -->
        <td><?= htmlspecialchars($p['categorie']) ?></td>

        <!-- Status -->
        <td>
            <?php
                $status = $p['status']; // par ex: actif, en attente, terminé
                $badgeClass = "";

                if ($status == "en cour") $badgeClass =  "bg-info";
                elseif ($status == "accepte") $badgeClass = "bg-success";
                elseif ($status == "refuse") $badgeClass ="bg-warning text-dark";
                else $badgeClass = "bg-secondary";
            ?>
            <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
        </td>
    </tr>
<?php } ?>
</tbody>

            </table>

          </div>
        </div>
      </div>

    </div>

  </div>
</div>
<!--fin dashboard-->


        
   
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <script src="assets/vendors/chart.js/chart.umd.js"></script>
  <script src="assets/vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="assets/js/off-canvas.js"></script>
  <script src="assets/js/hoverable-collapse.js"></script>
  <script src="assets/js/template.js"></script>
  <script src="assets/js/settings.js"></script>
  <script src="assets/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/proBanner.js"></script>

  <!-- End custom js for this page-->
  <script src="assets/js/jquery.cookie.js" type="text/javascript"></script>
  <script>
document.addEventListener("DOMContentLoaded", function () {
    const menu = document.querySelector(".profile-menu");
    const toggle = document.getElementById("profileToggle");

    toggle.addEventListener("click", function (e) {
        e.preventDefault();
        menu.classList.toggle("active");
    });

    // Fermer si on clique ailleurs
    document.addEventListener("click", function (e) {
        if (!menu.contains(e.target)) {
            menu.classList.remove("active");
        }
    });
});
</script>

</body>

</html>

