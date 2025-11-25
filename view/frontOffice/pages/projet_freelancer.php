<?php
session_start();
#include 'tete et pied/tetec.php';
include '../../../controller/roomC.php';



if (!isset($_SESSION['id_user'])) {
    echo "<script>
        alert('⚠️ Vous devez être connecté pour voir vos propositions.');
        window.location.href = '../login.php';
    </script>";
    exit();
}



$roomC = new RoomController();
$id_user = $_SESSION['id_user']; // maintenant c’est OK
$rooms = $roomC->getRoomsByUser($id_user);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link rel="stylesheet" href="../css/bootstrap.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/jquery-ui.css">
    <link rel="stylesheet" href="../css/nice-select.css">
<link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
 <title>Accueil Freelancer</title>
     <link rel="shortcut icon" type="image/x-icon" href="../img/logo.png?v=<?php echo time(); ?>">
     <?php
$img = "../../uploads/profiles/" . ($_SESSION['user']['image'] ?? "rass.jpg");
?>
    
</head>
<body>
    

 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">

<div class="header">

    <!-- TITRE -->
    

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
 
<div class="job_listing_area plus_padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
               <!-- Bulle flottante -->
<div id="ProposalModal" class="floating-btn-proposals" onclick="openProposalsModal()">
    📋
</div>

                <div class="job_filter white-bg">
                    <div class="form_inner white-bg">
                        <center><h3>Filtre</h3></center>
                        <form action="#">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="single_field">
                                        <input type="text" placeholder="recherche">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="single_field">
                                        <select class="wide">
                                            <option data-display="Categorie">Categorie</option>
                                            <option value="1">web</option>
                                            <option value="2">design</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="range_wrap">
                       <center> <label for="amount">💰 montant:</label></center>
                        <div id="slider-range"></div>
                        <p>
                            <input type="text" id="amount" readonly
                                style="border:0; color:#7A838B; font-size: 14px; font-weight:400;">
                        </p>
                    </div>
                    <div class="reset_btn">
                        <center><button class="btn btn-outline-info" type="submit">Recherche</button></center>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="job_lists m-0">
                    <div class="row">
                        <?php
                        include '../../../controller/publicationC.php';
                        $publicationC = new publicationController();
                      
                        $pub = $publicationC->list_pub_for_freelancer($id_user);
                        foreach ($pub as $publication) { ?>
                            <div class="col-lg-12 col-md-12">
                                <div class="single_jobs white-bg p-4 shadow-sm rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="text-primary fw-bold mb-0"><?php echo $publication['nom_pub']; ?></h4>
                                        <a class="btn btn-outline-info propose-btn"
                                            data-id="<?php echo $publication['id_pub']; ?>">
                                            Faire une proposition
                                        </a>
                                    </div>

                                    <!-- Description -->
                                    <p class="text-muted mb-3">
                                        <?php echo nl2br(htmlspecialchars($publication['description'])); ?>
                                    </p>

                                    <!-- Info Row -->
                                    <div class="row text-muted mb-2">
                                        <div class="col-md-3"><strong>💰 Budget:</strong>
                                            <?php echo $publication['budget']; ?> dt</div>
                                        <div class="col-md-3"><strong>⏱ Délai:</strong>
                                            <?php echo $publication['delai_requise'] . ' jours'; ?></div>
                                        <div class="col-md-3"><strong>📅 Date:</strong>
                                            <?php echo $publication['date_pub']; ?></div>
                                        <div class="col-md-3"><strong>🔖 Status:</strong>
                                            <?php echo $publication['status']; ?></div>
                                    </div>

                                    <!-- Proposition Form (UNCHANGED) -->
                                    <div class="ProposalForm_<?php echo $publication['id_pub']; ?>"
                                        id="proposalForm_<?php echo $publication['id_pub']; ?>"
                                        data-id="<?php echo $publication['id_pub']; ?>" style="display: none;">
                                        <form method="POST" id="propositionForm1"
                                            action="tete et pied/ajouter_proposition.php" novalidate>
                                            <input type="hidden" name="id_pub"
                                                value="<?php echo $publication['id_pub']; ?>">

                                            <div class="form-group">
                                                <label for="commentaire">Comment:</label>
                                                <textarea class="form-control" id="commentaire_propo" name="commentaire"
                                                    rows="3"></textarea>
                                                <small id="commentaireError" class="text-danger"></small>
                                            </div>

                                            <div class="form-group">
                                                <label for="montant_propo">Montant (dt):</label>
                                                <input type="number" id="montant_propo" class="form-control"
                                                    name="montant_propo" placeholder="Entrez le prix">
                                                <small id="montantError" class="text-danger"></small>
                                            </div>

                                            <div class="form-group">
                                                <label for="delai_estime">Délai (en jours):</label>
                                                <input type="number" id="delai_estime_propo" class="form-control"
                                                    placeholder="Entrez le délai en jours" name="delai_estime">
                                                <small id="delaiError" class="text-danger"></small>
                                            </div>

                                            <center>
                                                <button type="submit" class="btn btn-outline-info propose-btn">
                                                    Soumettre la proposition
                                                </button>
                                            </center>
                                        </form>
                                        <br>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>







<div id="proposalsModal" class="modal" style="display: none;">
    <div class="row">
        <div class="modal-content">
            <span class="close" onclick="closeProposalsModal()">&times;</span>

            <div class="col-lg-12">
                <ul class="nav nav-tabs custom-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="en-attente-tab" data-toggle="tab" href="#en-attente" role="tab"
                            aria-controls="en-attente" aria-selected="true">
                            <i class="fa fa-hourglass-half me-2"></i> En Attente
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="acceptee-tab" data-toggle="tab" href="#acceptee" role="tab"
                            aria-controls="acceptee" aria-selected="false">
                            <i class="fa fa-check-circle me-2"></i> Acceptée
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="refuse-tab" data-toggle="tab" href="#refuse" role="tab"
                            aria-controls="refuse" aria-selected="false">
                            <i class="fa fa-times-circle me-2"></i> Refusée
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="en-attente" role="tabpanel"
                        aria-labelledby="en-attente-tab">
                        <div class="job_lists m-0">
                            <div class="row">
                                <?php
                                include '../../../controller/propositionC.php';

                                $publicationC1 = new publicationController();
                                $pub1 = $publicationC1->list_pub_propo($id_user);
                                foreach ($pub1 as $publication) {
                                    $propositionC1 = new propositionController();
                                    $proposition = $propositionC1->getPropositionByUserAndPub($id_user, $publication['id_pub']);
                                    if ($proposition['status'] == 'en cours') {
                                        ?>
                                        <div class="col-lg-6 col-md-12 mb-4">
                                            <div class="single_jobs white-bg p-4 shadow-lg rounded border">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <input type="hidden" name="id_pub" value="<?= $publication['id_pub']; ?>">
                                                    <h4 class="mb-0 text-dark"><?= $publication['nom_pub']; ?></h4>
                                                </div>
                                                <div class="jobs_content mb-3">
                                                    <p class="text-muted"><?= $publication['description']; ?></p>
                                                </div>
                                                <div class="d-flex justify-content-between text-muted small">
                                                    <div class="col-md-3"><strong>💰 Budget:</strong>
                                                        <?php echo $publication['budget']; ?> dt</div>
                                                    <div class="col-md-3"><strong>⏱ Délai:</strong>
                                                        <?php echo $publication['delai_requise'] . ' jours'; ?></div>
                                                    <div class="col-md-3"><strong>📅 Date:</strong>
                                                        <?php echo $publication['date_pub']; ?></div>
                                                    <div class="col-md-3"><strong>🔖 Status:</strong>
                                                        <?php echo $publication['status']; ?></div>
                                                </div>
                                                <div class="proposition border rounded p-4 bg-light shadow-sm mt-3">
                                                    <h5 class="text-center text-primary">Votre Proposition en Attente</h5>
                                                    <div class="d-flex justify-content-end gap-2 mb-3">
                                                        <a href="tete et pied/delete_proposition.php?id_propo=<?= $proposition['id_propo']; ?>"
                                                            class="btn btn-outline-info btn-sm"
                                                            style=" border-color: #5bc0de;">Supprimer</a>


                                                        <a class="btn btn-outline-primary btn-sm propose-btn"
                                                            data-id="<?= $proposition['id_propo']; ?>">Modifier votre
                                                            Proposition</a>
                                                    </div>
                                                    <p class="mb-2 fw-semibold text-dark">
                                                        <?= htmlspecialchars($proposition['commentaire']); ?>
                                                    </p>
                                                    <div class="d-flex flex-wrap justify-content-between text-muted small">
                                                        <span><strong>Montant:</strong> <?= $proposition['montant_propo']; ?>
                                                            dt</span>
                                                        <span><strong>Délai:</strong>
                                                            <?= $proposition['delai_estime']; ?></span>
                                                        <span><strong>Status:</strong> <?= $proposition['status']; ?></span>
                                                        <span><strong>Date:</strong> <?= $proposition['date_propo']; ?></span>
                                                    </div>
                                                </div>

                                                <!-- Update Form -->
                                                <div class="ProposalForm_<?= $proposition['id_propo']; ?>"
                                                    id="proposalForm_<?= $proposition['id_propo']; ?>" style="display: none;">
                                                    <form method="POST" action="tete et pied/update_proposition.php"
                                                        id="propositionForm2">
                                                        <input type="hidden" name="id_propo"
                                                            value="<?= $proposition['id_propo']; ?>">
                                                        <input type="hidden" name="id_user"
                                                            value="<?= $proposition['id_user']; ?>">
                                                        <input type="hidden" name="id_pub"
                                                            value="<?= $proposition['id_pub']; ?>">
                                                        <input type="hidden" name="status"
                                                            value="<?= $proposition['status']; ?>">
                                                        <input type="hidden" name="date_propo"
                                                            value="<?= $proposition['date_propo']; ?>">

                                                        <div class="form-group mb-3">
                                                            <label for="commentaire">Commentaire:</label>
                                                            <textarea class="form-control" id="commentaire_propo_modif" name="commentaire_modif" rows="3"
                                                                ><?= $proposition['commentaire']; ?></textarea>
                                                            <small class="text-danger" id="commentaire_modifERROR"></small>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label for="montant_propo">Montant (dt):</label>
                                                            <input type="number" class="form-control" id="montant_propo_modif" name="montant_propo_modif"
                                                                value="<?= $proposition['montant_propo']; ?>" >
                                                            <small class="text-danger" id="montant_modifERROR"></small>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label for="delai_estime">Délai (en jours):</label>
                                                            <?php $delai_estime = str_replace(' jours', '', $proposition['delai_estime']); ?>
                                                            <input type="number" class="form-control" id="delai_estime_propo_modif" name="delai_estime_modif"
                                                                value="<?= $delai_estime; ?>" >
                                                            <small class="text-danger" id="delai_estime_modifERROR"></small>
                                                        </div>
                                                        <center><button type="submit"
                                                                class="btn btn-outline-primary propose-btn">Modifier</button>
                                                        </center>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } // hedhi status en attente mtaa l proposition
                                ?>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade" id="acceptee" role="tabpanel" aria-labelledby="acceptee-tab">
                        <div class="job_lists m-0">
                            <div class="row">
                                <?php
                                $publicationC2 = new publicationController();
                                $pub2 = $publicationC2->list_pub_propo($id_user);
                                foreach ($pub2 as $publication) {
                                    $propositionC2 = new propositionController();
                                    $proposition = $propositionC2->getPropositionByUserAndPub($id_user, $publication['id_pub']);
                                    if ($proposition['status'] == 'accepte') {
                                        ?>
                                        <div class="col-lg-6 col-md-12 mb-4">
                                            <div class="single_jobs white-bg p-4 shadow-lg rounded border">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <input type="hidden" name="id_pub" value="<?= $publication['id_pub']; ?>">
                                                    <h4 class="mb-0 text-dark"><?= $publication['nom_pub']; ?></h4>
                                                </div>
                                                <div class="jobs_content mb-3">
                                                    <p class="text-muted"><?= $publication['description']; ?></p>
                                                </div>
                                                <div class="d-flex justify-content-between text-muted small">
                                                    <div class="col-md-3"><strong>💰 Budget:</strong>
                                                        <?php echo $publication['budget']; ?> dt</div>
                                                    <div class="col-md-3"><strong>⏱ Délai:</strong>
                                                        <?php echo $publication['delai_requise'] . ' jours'; ?></div>
                                                    <div class="col-md-3"><strong>📅 Date:</strong>
                                                        <?php echo $publication['date_pub']; ?></div>
                                                    <div class="col-md-3"><strong>🔖 Status:</strong>
                                                        <?php echo $publication['status']; ?></div>
                                                </div>
                                                <div class="proposition border rounded p-4 bg-light shadow-sm mt-3">
                                                    <h5 class="text-center text-success">Votre Proposition Acceptée</h5>
                                                   <?php if (!empty($rooms)): ?>
    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="tete et pied/avance/chat.php?id_room=<?= $rooms[0]['id_room'] ?>" class="btn btn-primary">
            💬 Ouvrir discussion
        </a>
    </div>
<?php endif; ?>

                                                    <p class="mb-2 fw-semibold text-dark">
                                                        <?= htmlspecialchars($proposition['commentaire']); ?>
                                                    </p>
                                                    <div class="d-flex flex-wrap justify-content-between text-muted small">
                                                        <span><strong>Montant:</strong> <?= $proposition['montant_propo']; ?>
                                                            dt</span>
                                                        <span><strong>Délai:</strong>
                                                            <?= $proposition['delai_estime']; ?></span>
                                                        <span><strong>Status:</strong> <?= $proposition['status']; ?></span>
                                                        <span><strong>Date:</strong> <?= $proposition['date_propo']; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } // status accepte
                                ?>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade" id="refuse" role="tabpanel" aria-labelledby="refuse-tab">
                        <div class="job_lists m-0">
                            <div class="row">
                                <?php
                                $publicationC2 = new publicationController();
                                $pub2 = $publicationC2->list_pub_propo($id_user);
                                foreach ($pub2 as $publication) {
                                    $propositionC2 = new propositionController();
                                    $proposition = $propositionC2->getPropositionByUserAndPub($id_user, $publication['id_pub']);
                                    if ($proposition['status'] == 'refuse') {
                                        ?>
                                        <div class="col-lg-6 col-md-12 mb-4">
                                            <div class="single_jobs white-bg p-4 shadow-lg rounded border">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <input type="hidden" name="id_pub" value="<?= $publication['id_pub']; ?>">
                                                    <h4 class="mb-0 text-dark"><?= $publication['nom_pub']; ?></h4>
                                                </div>
                                                <div class="jobs_content mb-3">
                                                    <p class="text-muted"><?= $publication['description']; ?></p>
                                                </div>
                                                <div class="d-flex justify-content-between text-muted small">
                                                    <div class="col-md-3"><strong>💰 Budget:</strong>
                                                        <?php echo $publication['budget']; ?> dt</div>
                                                    <div class="col-md-3"><strong>⏱ Délai:</strong>
                                                        <?php echo $publication['delai_requise'] . ' jours'; ?></div>
                                                    <div class="col-md-3"><strong>📅 Date:</strong>
                                                        <?php echo $publication['date_pub']; ?></div>
                                                    <div class="col-md-3"><strong>🔖 Status:</strong>
                                                        <?php echo $publication['status']; ?></div>
                                                </div>
                                                <div class="proposition border rounded p-4 bg-light shadow-sm mt-3">
                                                    <h5 class="text-center text-danger">Votre Proposition Refusée</h5>
                                                    <p class="mb-2 fw-semibold text-dark">
                                                        <?= htmlspecialchars($proposition['commentaire']); ?>
                                                    </p>
                                                    <div class="d-flex flex-wrap justify-content-between text-muted small">
                                                        <span><strong>Montant:</strong> <?= $proposition['montant_propo']; ?>
                                                            dt</span>
                                                        <span><strong>Délai:</strong>
                                                            <?= $proposition['delai_estime']; ?></span>
                                                        <span><strong>Status:</strong> <?= $proposition['status']; ?></span>
                                                        <span><strong>Date:</strong> <?= $proposition['date_propo']; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } // status refusee
                                ?>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>
</div>


<style>
    html, body {
    overflow-x: hidden;
}

    .proposition_section {
        margin-top: 20px;
        padding: 10px;
        background-color: #f9f9f9;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .proposition {
        padding: 10px;
        margin-bottom: 15px;
        background-color: #fff;
        border-radius: 5px;
        border: 1px solid #bbb;
    }

    .proposition p {
        margin: 10px 0;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(235, 229, 229);
        background-color: rgba(42, 54, 110, 0.81);
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fff;
        padding: 20px;
        margin: 5% auto;
        border: 1px solid #888;
        width: 80%;
        max-width: 1500px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .proposal-item {
        border-bottom: 1px solid #ccc;
        padding: 10px 0;
    }

    .jobs_content {
        max-width: 100%;
        display: flex;
        flex-direction: column;
    }

    .jobs_content .text p {
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-height: 150px;
        overflow-y: auto;
    }

    .jobs_content .d-flex {
        flex-shrink: 0;
    }
    /* === Bulle flottante ronde (comme SmartLancer) === */
.floating-btn-proposals {
  position: fixed;
  bottom: 25px;
  right: 25px;
  width: 60px;
  height: 60px;
  background: #2b8a56; /* couleur verte SmartLancer */
  color: white;
  border-radius: 50%;
  font-size: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(0,0,0,0.25);
  transition: transform 0.2s ease, background 0.2s ease;
  z-index: 9999;
}

.floating-btn-proposals:hover {
  background: #237346;
  transform: scale(1.1);
}

.header {
            background: #2c8f4c; /* couleur développement durable */
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
    margin: 50px auto 0 auto; /* pas de marge en bas */
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
    margin-right:1500px;
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


.job_listing_area.plus_padding {
  padding-top: 142px;
  margin-left: -350px;
 color: #edf7f1;
}


</style>
<?php include 'tete et pied/pied.php'; ?>
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