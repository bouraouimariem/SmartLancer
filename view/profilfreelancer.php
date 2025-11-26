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
:root {
    --green: #0a5338;
    --green-dark: #075e3a;
    --bg-body: #eef3f7;
}

body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background-color: var(--bg-body);
}


header {
    background-color: var(--green);
    padding: 20px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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

/* Dropdown login/menu */
.nav-dropdown { display:inline-block; position:relative; margin-left:12px; }
.login-toggle { cursor:pointer; color:white; display:inline-block; padding:6px 12px; border-radius:6px; background-color:rgba(255,255,255,0.06); font-weight:600; }
.login-toggle:hover { background-color: rgba(255,255,255,0.16); }
.login-menu { display:none; position:absolute; right:0; top:calc(100% + 6px); background:#fff; color:#333; min-width:180px; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,0.12); overflow:hidden; z-index:2000; }
.login-menu a { display:block; padding:10px 12px; color:#0a5338; text-decoration:none; font-weight:600; border-bottom:1px solid #f0f0f0; }
.login-menu a:hover { background:#f6fff7; }
.nav-dropdown.open .login-menu { display:block; }

header nav a:hover {
    background-color: rgba(255,255,255,0.3);
}

/* ===== CONTAINER PROFIL ===== */
.container {
    max-width: 960px;
    margin: 40px auto;
    background: #fff;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.12);
}

h1, h2 {
    text-align: center;
    font-weight: 600;
    color: var(--green);
}

h1 { font-size: 32px; margin-bottom: 20px; }
h2 { font-size: 26px; margin-top: 30px; }

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
    border: 5px solid var(--green);
    box-shadow: 0 4px 14px rgba(0,0,0,0.15);
}

.info { text-align: center; }
.info p { margin: 6px 0; font-size: 17px; }

.divider {
    height: 4px;
    background: #d8ede2;
    margin: 35px 0;
    border-radius: 8px;
}

/* CARTE AVIS */
.review-card {
    background: #ffffff;
    padding: 22px;
    border-radius: 14px;
    margin-top: 22px;
    border: 1px solid #e3e8ec;
    transition: 0.3s;
    box-shadow: 0 3px 10px rgba(0,0,0,0.06);
}

.review-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 7px 20px rgba(0,0,0,0.12);
}

.stars { color: #ffb400; font-size: 22px; margin-bottom: 8px; }
.meta { color: #777; font-size: 14px; margin-bottom: 10px; }

form input[type="email"] {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-right: 6px;
    width: 180px;
    font-size: 14px;
}

.btn-action {
    margin-top: 10px;
    display: inline-block;
    padding: 8px 15px;
    background: var(--green);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.25s;
}

.btn-action:hover { background: var(--green-dark); transform: translateY(-2px); }

.btn-delete { background: #d33030 !important; }
.btn-delete:hover { background: #b02828 !important; }

.add-review { text-align:center; margin-top: 40px; }
.add-review a { font-size: 20px; padding: 12px 32px; border-radius: 12px; }


footer {
    color:white;
    text-align:center;
}

footer.main-footer {
    background-color: var(--green);
    padding:20px 0;
    font-size:14px;
    border-top:4px solid var(--green-dark);
}

footer.main-footer a {
    color:white;
    text-decoration:none;
    margin:0 10px;
}

footer.secondary-footer {
    background-color: var(--green-dark);
    padding:12px 0;
    font-size:12px;
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
                <a href="/validationmodule/view/backoffice/avisadmin.php">Dashboard</a>
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
                            <div class="reponse-item" data-id="<?= $r['id'] ?>" data-avis-id="<?= $avis['id'] ?>" data-nom="<?= htmlspecialchars($r['nom'], ENT_QUOTES) ?>" data-email="<?= htmlspecialchars($r['email'], ENT_QUOTES) ?>" data-contenu="<?= htmlspecialchars($r['contenu'], ENT_QUOTES) ?>" style="margin-bottom:10px;">
                                <div>
                                    <strong><?= htmlspecialchars($r['nom']) ?></strong>
                                    <span style="color:#777; font-size:12px; margin-left:8px;">le <?= htmlspecialchars(date('d/m/Y H:i', strtotime($r['created_at']))) ?></span>
                                </div>
                                <p style="margin:6px 0;" class="reponse-contenu"><?= nl2br(htmlspecialchars($r['contenu'])) ?></p>
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

<script src="reponse.js"></script>
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
