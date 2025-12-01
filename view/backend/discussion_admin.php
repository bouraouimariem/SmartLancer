<?php
require_once "../../Controller/DiscussionController.php";

$controller = new DiscussionController();
$id_reclamation = $_GET['id'] ?? null;
if (!$id_reclamation) die("Réclamation introuvable");

$messages = $controller->getDiscussion($id_reclamation);

// Envoyer un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['close'])) {
    $contenu = trim($_POST['contenu']);
    if (!empty($contenu)) {
        $controller->sendMessage($id_reclamation, 'admin', $contenu);
        header("Location: discussion_admin.php?id=$id_reclamation");
        exit;
    }
}

// Fermer la discussion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['close'])) {
    $controller->closeDiscussion($id_reclamation);
    header("Location: discussion_admin.php?id=$id_reclamation");
    exit;
}

$closed = $controller->isClosed($id_reclamation);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Discussion Admin</title>
<style>
body{background:#d6f5d6;font-family:Arial;padding:20px;}
.container{max-width:700px;margin:auto;background:#fff;padding:20px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.15);}
.message{padding:10px;border-radius:6px;margin-bottom:10px;}
.admin{background:#2e8b57;color:white;text-align:right;}
.user{background:#3498db;color:white;text-align:left;}
textarea{width:100%;padding:10px;margin-top:10px;border-radius:6px;}
button{padding:10px 20px;margin-top:5px;border:none;border-radius:6px;cursor:pointer;}
.send{background:#2e8b57;color:white;}
.send:hover{background:#256f47;}
.close{background:#e74c3c;color:white;}
.close:hover{background:#c0392b;}
.back{display:inline-block;margin-top:10px;text-decoration:none;color:white;background:#1a3d1a;padding:8px 12px;border-radius:6px;}
.back:hover{background:#13301a;}
.info{margin-bottom:10px;font-weight:bold;}
</style>
</head>
<body>
<div class="container">
<h2>Discussion Réclamation #<?= $id_reclamation ?></h2>

<div class="info">Statut : <?= $closed ? '<span style="color:red;">Fermée</span>' : '<span style="color:green;">Ouverte</span>' ?></div>

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
    <button type="submit" class="send">Envoyer</button>
</form>

<form method="POST" style="margin-top:10px;">
    <input type="hidden" name="close" value="1">
    <button type="submit" class="close">Fermer la discussion</button>
</form>
<?php else: ?>
<p style="color:red;font-weight:bold;">La discussion est fermée. L'utilisateur ne peut plus répondre.</p>
<?php endif; ?>

<a href="listReponses.php" class="back">← Retour aux Réponses</a>
</div>
</body>
</html>
