<?php
session_start();
require_once "../../Controller/DiscussionController.php";

$controller = new DiscussionController();

// Récupérer l'ID depuis GET (id ou id_reclamation)
$id_reclamation = $_GET['id'] ?? $_GET['id_reclamation'] ?? null;
if (!$id_reclamation) die("Réclamation introuvable");

// Récupération des messages et statut de la discussion
$messages = $controller->getDiscussion($id_reclamation);
$closed = $controller->isClosed($id_reclamation);

// Supprimer un message (uniquement ceux de l'utilisateur)
if (isset($_GET['delete_msg'])) {
    $msgId = (int)$_GET['delete_msg'];
    foreach ($messages as $msg) {
        if ($msg['id_message'] === $msgId && $msg['sender'] === 'user') {
            $controller->deleteMessage($msgId);
            break;
        }
    }
    header("Location: discussion_user.php?id=$id_reclamation");
    exit;
}

// Envoyer un message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu']);
    $fichier = $_FILES['fichier'] ?? null;

    if (!empty($contenu) || ($fichier && $fichier['error'] === UPLOAD_ERR_OK)) {
        $controller->sendMessage($id_reclamation, 'user', $contenu, $fichier);
        header("Location: discussion_user.php?id=$id_reclamation");
        exit;
    }
}

// Liste emojis
$emojis = ['😀','😂','😍','🤔','😡','😭','🙏','🎉','❤️','🔥','👍','👎','✅','❌'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Discussion Réclamation</title>
<link rel="stylesheet" href="css/chat.css">
<style>
.delete-msg {color:red;text-decoration:none;margin-left:5px;}
.closed {color:gray;margin:10px 0;}
</style>
</head>
<body>
<div class="chat-container">
    <header class="chat-header">
        <h2>Discussion Réclamation #<?= $id_reclamation ?></h2>
        <span class="chat-status"><?= $closed ? 'Fermée' : 'Ouverte' ?></span>
    </header>

    <div id="messages-list" class="messages-list">
        <?php foreach($messages as $msg): ?>
            <div class="message <?= $msg['sender']==='admin'?'admin':'user' ?>">
                <?php if(!empty($msg['fichier'])): ?>
                    <?php if(preg_match('/\.(jpg|jpeg|png|gif)$/i', $msg['fichier'])): ?>
                        <img src="../../uploads/<?= $msg['fichier'] ?>" class="msg-image">
                    <?php else: ?>
                        <a href="../../uploads/<?= $msg['fichier'] ?>" target="_blank" class="msg-file">📄 <?= htmlspecialchars($msg['fichier']) ?></a>
                    <?php endif; ?>
                <?php endif; ?>
                <p class="msg-text"><?= nl2br(htmlspecialchars($msg['contenu'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) ?></p>
                <span class="msg-time"><?= $msg['date_message'] ?></span>
                <?php if($msg['sender']==='user' && !$closed): ?>
                    <a href="?id=<?= $id_reclamation ?>&delete_msg=<?= $msg['id_message'] ?>" 
                       class="delete-msg"
                       onclick="return confirm('Supprimer ce message ?')">🗑</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if(!$closed): ?>
    <form method="POST" enctype="multipart/form-data" id="chatForm" class="chat-form">
        <div class="emoji-bar">
            <?php foreach($emojis as $e): ?>
                <button type="button" class="emoji-btn"><?= $e ?></button>
            <?php endforeach; ?>
        </div>
        <textarea name="contenu" id="chatInput" placeholder="Écrire un message..."></textarea>
        <div class="form-bottom">
            <input type="file" name="fichier" id="fileInput">
            <div id="preview" class="file-preview"></div>
            <button type="submit" class="send">Envoyer</button>
        </div>
    </form>
    <?php else: ?>
    <p class="closed">La discussion est fermée. Vous ne pouvez plus répondre.</p>
    <?php endif; ?>

    <a href="reclamations.php" class="back">← Retour </a>
</div>

<script src="public/js/chat.js"></script>
</body>
</html>
