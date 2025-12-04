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
  <div class="navbar-brand-wrapper d-flex justify-content-center">
    <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">
      <a class="navbar-brand brand-logo" style=" width: 20%;" href="index.php"><img  src="assets/images/logo.png"
          alt="logo" /></a>
          <br>
          <h1 style="font-family: 'Poppins', sans-serif; font-size: 24px; height: 20%;">SmartLancer</h1>
           </br>
           

        
    
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    <ul class="navbar-nav me-lg-4 w-100">
      <li class="nav-item nav-search d-none d-lg-block w-100">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text" id="search">
              <i class="mdi mdi-magnify"></i>
            </span>
          </div>
          <input type="text" class="form-control" placeholder="Search now" aria-label="search"
            aria-describedby="search">
        </div>
      </li>
    </ul>
    <ul class="navbar-nav navbar-nav-right">
      <li class="nav-item dropdown me-1">
        <a class="nav-link count-indicator dropdown-toggle d-flex justify-content-center align-items-center"
          id="messageDropdown" href="#" data-bs-toggle="dropdown">
          <i class="mdi mdi-message-text mx-0"></i>
          <span class="count"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
          <p class="mb-0 font-weight-normal float-left dropdown-header">Messages</p>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <img src="assets/images/faces/face4.jpg" alt="image" class="profile-pic">
            </div>
            <div class="preview-item-content flex-grow">
              <h6 class="preview-subject ellipsis font-weight-normal">David Grey
              </h6>
              <p class="font-weight-light small-text text-muted mb-0">
                The meeting is cancelled
              </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <img src="assets/images/faces/face2.jpg" alt="image" class="profile-pic">
            </div>
            <div class="preview-item-content flex-grow">
              <h6 class="preview-subject ellipsis font-weight-normal">Tim Cook
              </h6>
              <p class="font-weight-light small-text text-muted mb-0">
                New product launch
              </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <img src="assets/images/faces/face3.jpg" alt="image" class="profile-pic">
            </div>
            <div class="preview-item-content flex-grow">
              <h6 class="preview-subject ellipsis font-weight-normal"> Johnson
              </h6>
              <p class="font-weight-light small-text text-muted mb-0">
                Upcoming board meeting
              </p>
            </div>
          </a>
        </div>
      </li>
      <li class="nav-item dropdown me-4">
        <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center notification-dropdown"
          id="notificationDropdown" href="#" data-bs-toggle="dropdown">
          <i class="mdi mdi-bell mx-0"></i>
          <span class="count"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
          aria-labelledby="notificationDropdown">
          <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-success">
                <i class="mdi mdi-information mx-0"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">Application Error</h6>
              <p class="font-weight-light small-text mb-0 text-muted">
                Just now
              </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-warning">
                <i class="mdi mdi-weather-sunny mx-0"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">Settings</h6>
              <p class="font-weight-light small-text mb-0 text-muted">
                Private message
              </p>
            </div>
          </a>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-info">
                <i class="mdi mdi-account-box mx-0"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">New user registration</h6>
              <p class="font-weight-light small-text mb-0 text-muted">
                2 days ago
              </p>
            </div>
          </a>
        </div>
      </li>
      <li class="nav-item nav-profile dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
          <img src="assets/images/faces/face5.jpg" alt="profile" />
          <span class="nav-profile-name">Rasslene</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
          <a class="dropdown-item">
            <i class="mdi mdi-cog text-primary"></i>
            Settings
          </a>
          <a class="dropdown-item">
            <i class="mdi mdi-logout text-primary"></i>
            Logout
          </a>
        </div>
      </li>
      <li class="nav-item nav-settings d-none d-lg-flex">
        <a class="nav-link" href="#">
          <i class="mdi mdi-apps"></i>
        </a>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
      data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
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
            <h2 class="fw-bold">👋 Bienvenue dans le Dashboard Admin</h2>
            <p class="text-muted">
              Gérez les utilisateurs, projets, réclamations, blog, commentaires et statistiques.
            </p>
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
            <h2 class="fw-bold">124</h2>
            <p>+12 ce mois</p>
          </div>
        </div>
      </div>

      <div class="col-md-3 grid-margin stretch-card">
        <div class="card bg-success text-white">
          <div class="card-body">
            <h4 class="card-title">Projets</h4>
            <h2 class="fw-bold">58</h2>
            <p>+5 ce mois</p>
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

      <!-- Activités récentes -->
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">⚡ Activités Récentes</h4>

            <ul class="list-group">
              <li class="list-group-item">✔️ Nouveau projet ajouté : <b>E-commerce</b></li>
              <li class="list-group-item">👤 Nouvel utilisateur : <b>Rasslene</b></li>
              <li class="list-group-item">💬 Commentaire sur <b>Site Web</b></li>
              <li class="list-group-item">🗑 Utilisateur supprimé : <b>Sara</b></li>
              <li class="list-group-item">📅 Nouvel Commentaires publié</li>
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
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Rasslene</td>
                  <td>rass@example.com</td>
                  <td>25 Nov 2025</td>
                </tr>
                <tr>
                  <td>Aymen</td>
                  <td>aym@gmail.com</td>
                  <td>24 Nov 2025</td>
                </tr>
                <tr>
                  <td>Sarah</td>
                  <td>sarah@gmail.com</td>
                  <td>23 Nov 2025</td>
                </tr>
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
                <tr>
                  <td>Site e-commerce</td>
                  <td>Web</td>
                  <td><span class="badge bg-success">Actif</span></td>
                </tr>
                <tr>
                  <td>Application Mobile</td>
                  <td>Mobile</td>
                  <td><span class="badge bg-warning text-dark">En attente</span></td>
                </tr>
                <tr>
                  <td>Logo Branding</td>
                  <td>Design</td>
                  <td><span class="badge bg-info">Terminé</span></td>
                </tr>
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
</body>

</html>

