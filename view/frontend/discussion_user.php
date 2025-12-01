<?php
require_once "../../Controller/DiscussionController.php";

$controller = new DiscussionController();
$id_reclamation = $_GET['id'] ?? null;
if (!$id_reclamation) die("Réclamation introuvable");

$messages = $controller->getDiscussion($id_reclamation);
$closed = $controller->isClosed($id_reclamation);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$closed) {
    $contenu = trim($_POST['contenu']);
    if (!empty($contenu)) {
        $controller->sendMessage($id_reclamation, 'user', $contenu);
        header("Location: discussion_user.php?id=$id_reclamation");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Discussion Utilisateur</title>
<style>
body{background:#f0f0f0;font-family:Arial;padding:20px;}
.container{max-width:700px;margin:auto;background:#fff;padding:20px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.15);}
.message{padding:10px;border-radius:6px;margin-bottom:10px;}
.admin{background:#2e8b57;color:white;text-align:right;}
.user{background:#3498db;color:white;text-align:left;}
textarea{width:100%;padding:10px;margin-top:10px;border-radius:6px;}
button{padding:10px 20px;margin-top:5px;background:#3498db;color:white;border:none;border-radius:6px;cursor:pointer;}
button:hover{background:#2980b9;}
.back{display:inline-block;margin-top:10px;text-decoration:none;color:white;background:#1a3d1a;padding:8px 12px;border-radius:6px;}
.back:hover{background:#13301a;}
.closed{color:red;font-weight:bold;margin-top:10px;}
</style>
</head>
<body>
<div class="container">
<h2>Discussion Réclamation #<?= $id_reclamation ?></h2>

<div>
<?php foreach($messages as $msg): ?>
    <div class="message <?= $msg['sender'] === 'admin' ? 'admin' : 'user' ?>">
        <?= htmlspecialchars($msg['contenu']) ?> <br>
        <small><?= $msg['date_message'] ?></small>
    </div>
<?php endforeach; ?>
</div>

<?php if (!$closed): ?>
<form method="POST">
    <textarea name="contenu" rows="4" required placeholder="Votre message..."></textarea><br>
    <button type="submit">Envoyer</button>
</form>
<?php else: ?>
<p class="closed">La discussion est fermée. Vous ne pouvez plus répondre.</p>
<?php endif; ?>

<a href="index.php" class="back">← Retour</a>
</div>
</body>
</html>
