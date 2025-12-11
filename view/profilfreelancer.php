<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/avis.php';
require_once __DIR__ . '/../model/reponse.php';

// Profil du freelancer
$profil = [
    'nom' => 'Sarah',
    'specialite' => 'Développement Web & UI Design',
    'email' => 'sarah.freelance@example.com',
    'tarif' => '50 DT / heure',
    'photo' => '/validationmodule/view/images/profil5.jpeg'
];

$pdo = (new Database())->getConnection();
$avisModel = new Avis($pdo);
$avisList = $avisModel->getAllAvis();

$reponseModel = new Reponse($pdo);

// Traitement du like
if (isset($_POST['like_avis_id']) && isset($_POST['like_email'])) {
    $likeAvisId = (int)$_POST['like_avis_id'];
    $likeEmail = trim($_POST['like_email']);
    if ($likeEmail && filter_var($likeEmail, FILTER_VALIDATE_EMAIL)) {
        $avisModel->addLike($likeAvisId, $likeEmail);
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Freelancer + Avis</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
/* Formulaire de filtrage */
form {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    background-color: #f0f8ff; /* léger fond bleu clair */
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Labels */
form label {
    font-weight: bold;
    margin-right: 10px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Inputs et selects */
form input[type="text"],
form input[type="date"],
form input[type="email"],
form select {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    outline: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

form input[type="text"]:focus,
form input[type="date"]:focus,
form input[type="email"]:focus,
form select:focus {
    border-color: #1E90FF;
    box-shadow: 0 0 8px rgba(30,144,255,0.4);
}

/* Boutons du formulaire */
form button {
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    background-color: #1E90FF;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

/* Effet glow sur hover */
form button:hover {
    background-color: #1C86EE;
    box-shadow: 0 0 10px #1E90FF, 0 0 20px #1E90FF, 0 0 30px #1E90FF;
    transform: scale(1.05);
}

/* Lien Réinitialiser */
form a {
    text-decoration: none;
    color: #6a0dad; /* violet */
    font-weight: bold;
    transition: color 0.3s ease, transform 0.3s ease;
}

form a:hover {
    color: #4b0082;
    transform: scale(1.05);
}

/* Container */
.container {
    max-width: 1200px;
    margin: auto;
    background: #f9f9f9;
    padding: 30px 20px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
}

/* Title */
h1 {
    text-align: center;
    color: #1E90FF;
    margin-bottom: 25px;
}

h2 {
    text-align: center;
    color: #1E90FF;
    margin-bottom: 25px;
}

/* Profile and info styling */
.profile-picture {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.profile-picture img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid #1E90FF;
    box-shadow: 0 4px 14px rgba(0,0,0,0.15);
}

.info { 
    text-align: center; 
}

.info p { 
    margin: 6px 0; 
    font-size: 17px; 
}

.divider {
    height: 4px;
    background: #e0f0ff;
    margin: 35px 0;
    border-radius: 8px;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #1E90FF;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f8ff;
}

tr:hover {
    background-color: #e0f0ff;
    transition: background-color 0.3s ease;
}

/* CARTE AVIS / Review Card */
.review-card {
    background: #ffffff;
    padding: 22px;
    border-radius: 14px;
    margin-top: 22px;
    border: 1px solid #e0f0ff;
    transition: 0.3s;
    box-shadow: 0 3px 10px rgba(0,0,0,0.06);
}

.review-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 7px 20px rgba(0,0,0,0.12);
    border-color: #1E90FF;
}

.stars { color: #ffb400; font-size: 22px; margin-bottom: 8px; }
.meta { color: #777; font-size: 14px; margin-bottom: 10px; }

/* Buttons Styling */
.button {
    padding: 8px 14px;
    border-radius: 8px;
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
    cursor: pointer;
    border: none;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

/* Glow animation */
.button::after {
    content: "";
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, rgba(30,144,255,0.5), rgba(0,255,255,0.5), rgba(30,144,255,0.5));
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
    transform: rotate(45deg);
}

.button:hover::after {
    opacity: 1;
}

/* Button Variants */
.btn-action {
    padding: 8px 15px;
    border-radius: 8px;
    background-color: #1E90FF;
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
    cursor: pointer;
    border: none;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    z-index: 1;
}

.btn-action:hover {
    background-color: #1C86EE;
    transform: scale(1.08);
    box-shadow: 0 0 10px #1E90FF;
}

.edit {
    background-color: #1E90FF;
    z-index: 1;
}

.edit:hover {
    transform: scale(1.08);
}

.delete {
    background-color: #FF4C4C;
    z-index: 1;
}

.delete:hover {
    transform: scale(1.08);
}

.btn-delete { 
    background: #FF4C4C !important; 
}

.btn-delete:hover { 
    background: #D33030 !important; 
}

.chat {
    background-color: #32CD32;
    z-index: 1;
}

.chat:hover {
    transform: scale(1.08);
}

.back, .add-new {
    background-color: #1E90FF;
    margin-right: 10px;
    z-index: 1;
}

.back:hover, .add-new:hover {
    transform: scale(1.08);
}

.add-review { 
    text-align: center; 
    margin-top: 40px; 
}

.add-review a { 
    font-size: 16px; 
    padding: 12px 32px; 
    border-radius: 12px;
    background-color: #1E90FF;
    color: #fff;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.add-review a:hover {
    background-color: #1C86EE;
    transform: scale(1.05);
}

/* Back-zone container */
.back-zone {
    margin-top: 20px;
    text-align: center;
}

.response {
    background: linear-gradient(135deg, #7b2fff, #5a00e6);
    color: #fff;
    font-weight: bold;
    border-radius: 8px;
    padding: 8px 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    z-index: 1;
}

.response::before {
    content: "👁️"; /* icône œil */
    font-size: 16px;
}

.response:hover {
    transform: scale(1.08);
    background: linear-gradient(135deg, #5a00e6, #3c009c);
}

/* Responses list styling */
.responses-list {
    margin-top: 12px;
}

.reponse-item {
    padding: 10px;
    border-left: 3px solid #1E90FF;
    background: #f9f9f9;
    border-radius: 8px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.reponse-item:hover {
    background: #fff;
    box-shadow: 0 3px 10px rgba(30,144,255,0.2);
}

.reponse-contenu {
    color: #333;
    line-height: 1.6;
}

/* Footer */
footer {
    color: white;
    text-align: center;
}

footer.main-footer {
    background-color: #1E90FF;
    padding: 20px 0;
    font-size: 14px;
    border-top: 4px solid #1C86EE;
    margin-top: 40px;
}

footer.main-footer a {
    color: white;
    text-decoration: none;
    margin: 0 10px;
}

footer.secondary-footer {
    background-color: #1C86EE;
    padding: 12px 0;
    font-size: 12px;
}

/* Global styles */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html, body {
    min-height: 100%;
    width: 100%;
    font-family: "Poppins", sans-serif;
    background: linear-gradient(135deg, #d0e8ff, #f0f9ff);
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    padding: 20px 0;
}

/* Header */
header {
    background-color: #1E90FF;
    padding: 20px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    width: 100%;
}

header img.logo {
    height: 100px;
    border-radius: 8px;
}

header nav a {
    color: white;
    text-decoration: none;
    font-weight: 600;
    margin-left: 20px;
    padding: 6px 12px;
    border-radius: 6px;
    background-color: rgba(255,255,255,0.1);
    transition: 0.3s;
}

header nav a:hover {
    background-color: rgba(255,255,255,0.3);
}

/* Dropdown login/menu */
.nav-dropdown { 
    display: inline-block; 
    position: relative; 
    margin-left: 12px; 
}

.login-toggle { 
    cursor: pointer; 
    color: white; 
    display: inline-block; 
    padding: 6px 12px; 
    border-radius: 6px; 
    background-color: rgba(255,255,255,0.06); 
    font-weight: 600;
    transition: 0.3s;
}

.login-toggle:hover { 
    background-color: rgba(255,255,255,0.16); 
}

.login-menu { 
    display: none; 
    position: absolute; 
    right: 0; 
    top: calc(100% + 6px); 
    background: #fff; 
    color: #333; 
    min-width: 180px; 
    border-radius: 8px; 
    box-shadow: 0 8px 24px rgba(0,0,0,0.12); 
    overflow: hidden; 
    z-index: 2000; 
}

.login-menu a { 
    display: block; 
    padding: 10px 12px; 
    color: #1E90FF; 
    text-decoration: none; 
    font-weight: 600; 
    border-bottom: 1px solid #f0f0f0;
    transition: 0.3s;
}

.login-menu a:hover { 
    background: #f0f8ff; 
}

.nav-dropdown.open .login-menu { 
    display: block; 
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        padding: 25px 15px;
        width: 95%;
    }

    button, .button {
        font-size: 15px;
        padding: 10px 16px;
    }

    textarea { 
        height: 100px; 
    }
    
    form {
        flex-direction: column;
    }
    
    form input, form select {
        width: 100%;
    }
}
</style>
</head>

<body>
<header>
    <img src="/validationmodule/view/images/logo.png" alt="Logo SmartLancer" class="logo">
    <nav>
        <a href="accueil.html">Accueil</a>
        <div class="nav-dropdown" id="nav-login">
            <span class="login-toggle" id="login-toggle" tabindex="0">Se connecter ▾</span>
            <div class="login-menu" id="login-menu">
                <a href="/validationmodule/view/backoffice/dashboardavis.php">Dashboard</a>
            </div>
        </div>
    </nav>
</header>

<div class="container">

    <h1>Profil Freelancer</h1>

    <div class="profile-picture">
        <img src="<?= htmlspecialchars($profil['photo']) ?>" alt="Profil">
    </div>

    <div class="info">
        <p><strong>Nom :</strong> <?= htmlspecialchars($profil['nom']) ?></p>
        <p><strong>Spécialité :</strong> <?= htmlspecialchars($profil['specialite']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($profil['email']) ?></p>
        <p><strong>Tarif :</strong> <?= htmlspecialchars($profil['tarif']) ?></p>
    </div>

    <div class="divider"></div>

    <h2>⭐ Avis des Clients ⭐</h2>

    <?php if (empty($avisList)): ?>
        <p style="text-align:center; color:#888;">Aucun avis pour le moment.</p>

    <?php else: ?>
        <?php foreach ($avisList as $avis): ?>
            <div class="review-card">
                <div class="stars">
                    <?= str_repeat('⭐', intval($avis['note'])) ?>
                </div>

                <strong><?= htmlspecialchars($avis['nom']) ?></strong>
                <p class="meta">Publié le <?= htmlspecialchars(date("d/m/Y", strtotime($avis['created_at']))) ?></p>

                <p><?= nl2br(htmlspecialchars($avis['contenu'])) ?></p>

                <!-- Like -->
                <form method="post" style="display:inline;">
                    <input type="hidden" name="like_avis_id" value="<?= $avis['id'] ?>">
                    <input type="text" name="like_email" placeholder="Votre email">

                    <?php 
                        $likesCount = $avisModel->getLikesCount($avis['id']);
                        $userEmail = isset($_POST['like_email']) ? $_POST['like_email'] : '';
                        $alreadyLiked = $userEmail && $avisModel->hasUserLiked($avis['id'], $userEmail);
                    ?>

                    <button type="submit" class="btn-action" <?= $alreadyLiked ? 'disabled' : '' ?>>
                        👍 Like (<?= $likesCount ?>)
                    </button>

                    <?php if ($alreadyLiked): ?>
                        <span style="color:#07a96c; font-size:13px; margin-left:8px;">Vous avez déjà liké</span>
                    <?php endif; ?>
                </form>

                <a href="/validationmodule/view/avisfront.php?id=<?= $avis['id'] ?>" class="btn-action">✏️ Modifier</a>
                <a href="/validationmodule/view/deleteAvis.php?id=<?= $avis['id'] ?>" class="btn-action btn-delete">🗑️ Supprimer</a>
                
                <!-- Réponses associées -->
                <?php
                    $responses = $reponseModel->getByAvisId($avis['id']);
                ?>
                <div id="responses-<?= $avis['id'] ?>" class="responses-list" style="margin-top:12px;">
                    <?php if (!empty($responses)): ?>
                        <div style="padding:10px; border-left:3px solid #eee; background:#fafafa; border-radius:8px;">
                        <?php foreach ($responses as $r): ?>
                            <div class="reponse-item" data-id="<?= $r['id'] ?>" data-avis-id="<?= $avis['id'] ?>" data-nom="<?= htmlspecialchars($r['nom'], ENT_QUOTES) ?>" data-email="<?= htmlspecialchars($r['email'], ENT_QUOTES) ?>" data-contenu="<?= htmlspecialchars($r['contenu'], ENT_QUOTES) ?>" data-piece="<?= isset($r['piece_jointe']) ? htmlspecialchars($r['piece_jointe'], ENT_QUOTES) : '' ?>" data-niveau="<?= isset($r['niveau_sensitive']) ? htmlspecialchars($r['niveau_sensitive'], ENT_QUOTES) : '' ?>" style="margin-bottom:10px;">
                                <div>
                                    <strong><?= htmlspecialchars($r['nom']) ?></strong>
                                    <span style="color:#777; font-size:12px; margin-left:8px;">le <?= htmlspecialchars(date('d/m/Y H:i', strtotime($r['created_at']))) ?></span>
                                </div>
                                <p style="margin:6px 0;" class="reponse-contenu"><?= nl2br(htmlspecialchars($r['contenu'])) ?></p>
                                <?php if (!empty($r['niveau_sensitive'])): ?>
                                    <div style="margin-top:8px;color:#555;font-size:13px">
                                        <?php if (!empty($r['niveau_sensitive'])): ?><span><strong>Niveau:</strong> <?= htmlspecialchars($r['niveau_sensitive']) ?></span><?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div style="display:flex; gap:8px;">
                                    <button class="btn-action edit-reponse" data-id="<?= $r['id'] ?>">Modifier</button>
                                    <button class="btn-action btn-delete delete-reponse" data-id="<?= $r['id'] ?>">Supprimer</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bouton pour ouvrir la modal d'ajout -->
                <div style="margin-top:12px;">
                    <button class="btn-action open-reponse-btn" data-avis-id="<?= $avis['id'] ?>">Ajouter une réponse</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="add-review">
        <a href="/validationmodule/view/avisfront.php" class="btn-action">+ Ajouter un avis</a>
    </div>

</div>

<!-- FOOTERS -->
<footer class="main-footer">
    <p>&copy; 2025 SmartLancer. Tous droits réservés.</p>
    <p>
        <a href="accueil.html">Accueil</a>
        <a href="freelancers.php">Freelancers</a>
        <a href="contact.html">Contact</a>
    </p>
</footer>

<footer class="secondary-footer">
    <p>Conçu avec ❤️ par l'équipe SmartLancer</p>
</footer>

<?php
    $scriptPath = __DIR__ . '/reponse.js';
    $ver = file_exists($scriptPath) ? filemtime($scriptPath) : time();
?>
<script src="reponse.js?v=<?php echo $ver; ?>"></script>
<script>
// Toggle login dropdown
(function(){
    const toggle = document.getElementById('login-toggle');
    const dropdown = document.getElementById('nav-login');
    if (!toggle || !dropdown) return;

    function closeDropdown() { dropdown.classList.remove('open'); }
    function openDropdown() { dropdown.classList.add('open'); }

    toggle.addEventListener('click', function(e){
        e.stopPropagation();
        dropdown.classList.toggle('open');
    });

    // close on outside click
    document.addEventListener('click', function(){ closeDropdown(); });

    // allow keyboard open (Enter/Space)
    toggle.addEventListener('keydown', function(e){
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); dropdown.classList.toggle('open'); }
    });
})();
</script>
</body>
</html>
