<?php
include '../../../controller/publicationC.php';
include '../../../controller/propositionC.php';
include '../../../controller/roomC.php';
include '../../../controller/messageC.php';

$publicationC = new publicationController();
session_start();
$roomC = new RoomController();
$id_user = $_SESSION['id_user']; // id du client connecté
$rooms = $roomC->getRoomsByUser($id_user);

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$id_user = $_SESSION['user']['id_utilisateur']; // ID de l’utilisateur connecté
$list = $publicationC->listpub_for_user($id_user);

?>





<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil Client</title>
     <link rel="shortcut icon" type="image/x-icon" href="../img/logo.png?v=<?php echo time(); ?>">
     <?php
$img = "../../uploads/profiles/" . ($_SESSION['user']['image'] ?? "rass.jpg");
?>
<div class="header">

    <!-- TITRE -->
      <img src="../img/logo.png" class="immg">


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
 
<div class="main-container">
    <div class="left-content">
        <h3>📢 Vos Publications</h3>

        <?php foreach ($list as $publication) {
            $propositionC = new propositionController();
            $list_propo = $propositionC->list_propo_client($publication['id_pub']);
        ?>
        <div class="publication-card">
            <div class="card-header">
                <h4><?= htmlspecialchars($publication['nom_pub']) ?></h4>
                <div class="actions">
                    <?php if ($publication['status'] == 'en cours') { ?>
                        <button class="btn blue" onclick="toggleForm_proposition(<?= $publication['id_pub'] ?>)">Propositions</button>
                        <a href="tete et pied/delete_publication.php?id_pub=<?= $publication['id_pub'] ?>" class="btn red">Supprimer</a>
                        <button class="btn green" onclick="toggleForm(<?= $publication['id_pub'] ?>)">Modifier</button>
                    <?php } else { 
                        $hasRoom = false;
                        $id_room = null;
                        foreach ($rooms as $room) {
                            if ($room['id_pub'] == $publication['id_pub']) {
                                $hasRoom = true;
                                $id_room = $room['id_room'];
                                break;
                            }
                        }
                        if ($hasRoom): ?>
                            <a href="tete et pied/avance/chat.php?id_room=<?= $id_room ?>" class="btn green">💬 Discussion</a>
                        <?php endif; 
                    } ?>
                </div>
            </div>

            <p class="desc"><?= htmlspecialchars($publication['description']) ?></p>

            <div class="info-grid">
                <div><strong>💰 Budget:</strong> <?= $publication['budget'] ?> dt</div>
                <div><strong>⏱ Délai:</strong> <?= $publication['delai_requise'] ?> jours</div>
                <div><strong>📅 Date:</strong> <?= $publication['date_pub'] ?></div>
                <div><strong>🔖 Statut:</strong> <?= $publication['status'] ?></div>
            </div>

            <!-- === Formulaire de modification === -->
            <div id="edit-form-<?= $publication['id_pub'] ?>" class="edit-form">
                <form action="tete et pied/update_publication.php" method="POST">
                    <input type="hidden" name="id_pub" value="<?= $publication['id_pub'] ?>">
                    <input type="hidden" name="id_user" value="<?= $publication['id_user'] ?>">
                    <input type="hidden" name="categorie" value="<?= $publication['categorie'] ?>">
                    <input type="hidden" name="date_pub" value="<?= $publication['date_pub'] ?>">
                    <input type="hidden" name="status" value="<?= $publication['status'] ?>">

                    <label>Nom</label>
                    <input type="text" name="nom_pub_modif" value="<?= $publication['nom_pub'] ?>">

                    <label>Description</label>
                    <textarea name="description_modif"><?= $publication['description'] ?></textarea>

                    <label>Budget</label>
                    <input type="number" name="budget_modif" value="<?= $publication['budget'] ?>">

                    <label>Délai</label>
                    <input type="number" name="delai_modif" value="<?= $publication['delai_requise'] ?>">

                    <button type="submit" class="btn green">💾 Enregistrer</button>
                </form>
            </div>

            <!-- === Liste des propositions === -->
            <div id="proposition-form-<?= $publication['id_pub'] ?>" class="prop-list">
                <h5>📩 Propositions reçues</h5>
                <?php if (!empty($list_propo)): ?>
                    <?php foreach ($list_propo as $proposition): ?>
                        <div class="proposal">
                            <p><?= htmlspecialchars($proposition['commentaire']) ?></p>
                            <small>
                                💰 <?= $proposition['montant_propo'] ?> dt | 
                                ⏱ <?= $proposition['delai_estime'] ?> jours | 
                                📅 <?= $proposition['date_propo'] ?>
                            </small>
                            <div class="actions">
                                <?php if ($proposition['status'] != 'refuse') { ?>
                                    <a href="tete et pied/refuse_proposition_client.php?id_propo=<?= $proposition['id_propo'] ?>" class="btn red">Refuser</a>
                                    <a href="tete et pied/avance/add_room.php?id_propo=<?= $proposition['id_propo'] ?>" class="btn green">Accepter</a>
                                <?php } else { ?>
                                    <a href="tete et pied/annulerefuse_proposition_client.php?id_propo=<?= $proposition['id_propo'] ?>" class="btn gray">Annuler le refus</a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty">Aucune proposition pour cette publication.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- ======== Section Création Projet ======== -->
    <div class="right-content">
        <h3>➕ Créer un Nouveau Projet</h3>
        <form method="POST" action="tete et pied/ajouter_publication.php">
            <label>Nom du Projet</label>
            <input type="text" name="nom_pub" placeholder="Ex: Application Mobile Durable">

            <label>Description</label>
            <textarea name="description" placeholder="Décrivez votre idée..."></textarea>

            <label>Budget (DT)</label>
            <input type="number" name="budget" placeholder="1000">

            <label>Délai (jours)</label>
            <input type="number" name="delai" placeholder="15">

            <label>Catégories</label>
            <div class="categories">
                <label><input type="checkbox" name="categories[]" value="web"> Web</label>
                <label><input type="checkbox" name="categories[]" value="mobile"> Mobile</label>
                <label><input type="checkbox" name="categories[]" value="design"> Design</label>
                <label><input type="checkbox" name="categories[]" value="marketing"> Marketing</label>
                <label><input type="checkbox" name="categories[]" value="ai"> Intelligence Artificielle</label>
            </div>

            <button class="bttn"> PUBLIER</button> 
        </form>
    </div>
</div>

<!-- ======== STYLES ======== -->
<style>
    body {
        font-family: "Poppins", sans-serif;
        background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        margin: 0;
        padding: 0;
        color: #333;
    }

    
    .main-container {
        display: flex;
        justify-content: space-between;
        gap: 30px;
        padding: 40px;
           }

    .left-content, .right-content {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .left-content {
        width: 65%;
    }

    .right-content {
        width: 30%;
    }

    .publication-card {
        background: #fafafa;
        border-left: 5px solid #4caf50;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn {
        padding: 8px 14px;
        border-radius: 8px;
        color: white;
        font-size: 14px;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn.green { background: #43a047; }
    .btn.blue { background: #1e88e5; }
    .btn.red { background: #e53935;
     }
    .btn.gray { background: #757575; }
    .btn.full { width: 100%; margin-top: 15px; }

    .btn:hover { opacity: 0.85; }
     .actions {
    display: flex;
    justify-content: flex-end; /* ✅ Aligne les boutons à droite */
    gap: 10px; /* ✅ Espace entre les deux boutons */
    margin-top: 10px;
}

.actions .btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.3s ease;
}

/* ✅ Bouton Refuser (rouge) */
.actions .btn.red {
    background-color: #e74c3c;
}
.actions .btn.red:hover {
    background-color: #c0392b;
}

/* ✅ Bouton Accepter (vert) */
.actions .btn.green {
    background-color: #27ae60;
}
.actions .btn.green:hover {
    background-color: #1e8449;
}

    input, textarea {
        width: 100%;
        border: 1px solid #ccc;
        padding: 8px;
        border-radius: 8px;
        margin-bottom: 12px;
        font-size: 14px;
    }

    textarea {
        resize: vertical;
        min-height: 80px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-top: 10px;
        color: #555;
        font-size: 14px;
    }

    .proposal {
        background: #e8f5e9;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .prop-list {
        margin-top: 10px;
        background: #f1f8e9;
        border-radius: 8px;
        padding: 10px;
    }

    .edit-form {
        display: none;
        background: #f9fff9;
        border: 1px solid #c8e6c9;
        padding: 15px;
        margin-top: 10px;
        border-radius: 10px;
    }

    h3, h4, h5 {
        color: #2e7d32;
    }

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
    color: black;
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




    /* Animation d'ouverture/fermeture des propositions */
.prop-list {
    margin-top: 10px;
    background: #f1f8e9;
    border-radius: 8px;
    padding: 0 10px;
    overflow: hidden;
    max-height: 0;
    transition: max-height 2s ease, padding 0.2s ease;
}

.prop-list.expanded {
    padding: 10px;
    max-height: 800px; /* Ajuste selon la taille de ton contenu */
}

/* ================================
       🌙 MODE SOMBRE / CONTENT
================================ */

body.dark .left-content,
body.dark .right-content {
    background: #24382b;           /* Bloc sombre */
    color: #e0e0e0;                /* Texte clair */
    box-shadow: 0 3px 12px rgba(0,0,0,0.4);
}

/* Carte publication */
body.dark .publication-card {
    background: #2b4636;
    border-left-color: #4caf50;
    color: #abff9bff;
}

/* Proposals (petits blocs verts) */
body.dark .proposal {
    background: #2d4a37;
}

/* Conteneur liste propositions */
body.dark .prop-list {
    background: #253a2d;
}

/* Formulaire d’édition */
body.dark .edit-form {
    background: #2b4636;
    border-color: #3c6e52;
    color: #e0e0e0;
}

/* Inputs sombres */
body.dark input,
body.dark textarea {
    background: #1f2f26;
    border: 1px solid #3c6e52;
    color: white;
}

body.dark h4
{
    color: #78cb9cff;
}

body.dark h3
{
    color: #4eb18bff;
}


body.dark .info-grid
{
    color:white;
}


body.dark .bttn {
  --green: #1BFD9C;
  width: 400px;
  font-size: 15px;
  padding: 0.7em 2.7em;
  letter-spacing: 0.06em;
  position: relative;
  font-family: inherit;
  border-radius: 0.6em;
  margin-top:17px;
  margin-left:8px;
  overflow: hidden;
  transition: all 0.3s;
  line-height: 1.4em;
  border: 2px solid var(--green);
  background: linear-gradient(to right, rgba(27, 253, 156, 0.1) 1%, transparent 40%,transparent 60% , rgba(27, 253, 156, 0.1) 100%);
  color: var(--green);
  box-shadow: inset 0 0 10px rgba(27, 253, 156, 0.4), 0 0 9px 3px rgba(27, 253, 156, 0.1);
  cursor:pointer;

}

.bttn {
  --green: #098b53ff;
  width: 400px;
  font-size: 15px;
  padding: 0.7em 2.7em;
  letter-spacing: 0.06em;
  position: relative;
  font-family: inherit;
  border-radius: 0.6em;
  margin-top:17px;
  margin-left:8px;
  overflow: hidden;
  transition: all 0.3s;
  line-height: 1.4em;
  border: 2px solid var(--green);
  background: linear-gradient(to right, rgba(23, 203, 125, 0.74) 1%, transparent 40%,transparent 60% , rgba(23, 203, 125, 0.74) 100%);
  color: var(--green);
  box-shadow: inset 0 0 10px rgba(9, 187, 110, 0.67), 0 0 9px 3px rgba(9, 187, 110, 0.67);
  cursor:pointer;

}

.bttn:hover {
  box-shadow: inset 0 0 10px rgba(27, 253, 156, 0.6), 0 0 9px 3px rgba(27, 253, 156, 0.2);
}

.bttn:before {
  content: "";
  position: absolute;
  left: -4em;
  width: 4em;
  height: 100%;
  top: 0;
  transition: transform .4s ease-in-out;
  background: linear-gradient(to right, transparent 1%, rgba(27, 253, 156, 0.1) 40%,rgba(27, 253, 156, 0.1) 60% , transparent 100%);
}

.bttn:hover:before {
  transform: translateX(15em);
}


</style>

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

function toggleForm_proposition(id) {
    var form = document.getElementById('proposition-form-' + id);
    form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
}

function toggleForm(id) {
    var form = document.getElementById('edit-form-' + id);
    form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
}
function toggleForm_proposition(id) {
    var form = document.getElementById('proposition-form-' + id);
    form.classList.toggle("expanded");
}
</script>
