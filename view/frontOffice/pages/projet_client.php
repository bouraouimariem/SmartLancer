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
     <link rel="stylesheet" href="../css/projet_client.css">

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
 <a href="accueil_client.php" class="back-btn" >
    ⬅ Retour
</a>

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
        <form method="POST" action="tete et pied/ajouter_publication.php" onsubmit="return validerForm();">

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

<a href="../../../pdf/publications_pdf.php" class="btn-download-pdf" target="_blank">
    <i class="bi bi-file-earmark-pdf"></i> Télécharger les Publications
</a>
<style>.btn-download-pdf {
    font-size:10px;
    display: inline-block;
    padding: 8px 15px;
    background-color: #2c8f4c;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    margin-left: 50%;
    margin-top: 40px;
}

.btn-download-pdf:hover {
    color: black;
    background-color: #7bd898ff;
}
</style>
<script>

function validerForm() {
    let nom = document.querySelector('input[name="nom_pub"]').value.trim();
    let desc = document.querySelector('textarea[name="description"]').value.trim();
    let budget = document.querySelector('input[name="budget"]').value.trim();
    let delai = document.querySelector('input[name="delai"]').value.trim();
    let categories = document.querySelectorAll('input[name="categories[]"]:checked');

    // Vérifier Nom du projet
    if (nom === "") {
        alert("Veuillez entrer un nom de projet.");
        return false;
    }

    // Vérifier Description
    if (desc.length < 10) {
        alert("La description doit contenir au moins 10 caractères.");
        return false;
    }

    // Vérifier Budget
    if (budget === "" || budget <= 0) {
        alert("Veuillez entrer un budget valide.");
        return false;
    }

    // Vérifier Délai
    if (delai === "" || delai <= 0) {
        alert("Veuillez entrer un délai en jours.");
        return false;
    }

    // Vérifier Catégories
    if (categories.length === 0) {
        alert("Veuillez choisir au moins une catégorie.");
        return false;
    }

    return true; // Autorise l'envoi du formulaire
}

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
