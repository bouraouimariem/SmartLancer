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
  <div class="navbar-brand-wrapper d-flex justify-content-center">
    <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">
      <a class="navbar-brand brand-logo" style=" width: 30%;" href="../index.php"><img  src="../assets/images/logo.png"
        alt="logo" /></a>
        <br>
        <h1 style="font-family: 'Poppins', sans-serif; font-size: 24px; height: 20%;">Lanci</h1>
         </br>
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
        <span class="mdi mdi-sort-variant"></span>
      </button>
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
              <img src="../assets/images/faces/face4.jpg" alt="image" class="profile-pic">
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
              <img src="../assets/images/faces/face2.jpg" alt="image" class="profile-pic">
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
              <img src="../assets/images/faces/face3.jpg" alt="image" class="profile-pic">
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
          <img src="../assets/images/faces/face5.jpg" alt="profile" />
          <span class="nav-profile-name">Louis Barnett</span>
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
        <a class="nav-link" href="../../frontoffice/pages/projet_admin.php">
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
            <a class="nav-link" href="#">
              <i class="mdi mdi-heart menu-icon"></i>
              <span class="menu-title">Gestion des Utilisateurs</span>
             <!---- <i class="menu-arrow"></i>-->
            </a>
          </li>    
          <li class="nav-item">
            <a class="nav-link" href="../pages/gestion_projets/projets.php">
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
            <a class="nav-link" href="#">
              <i class="mdi mdi-grid-large menu-icon"></i>
              <span class="menu-title">Gestion du Blog</span>
            </a>
          </li>    
          <li class="nav-item">
            <a class="nav-link" href="#">
              <i class="mdi mdi-emoticon menu-icon"></i>
              <span class="menu-title">Gestion des Evenements</span>
            </a>
          </li>    
  </ul>
</nav>