<?php
require_once "../../Controller/DiscussionController.php";

$controller = new DiscussionController();
$id_reclamation = $_GET['id'] ?? null;
if (!$id_reclamation) die("Réclamation introuvable");

$messages = $controller->getDiscussion($id_reclamation);
$closed = $controller->isClosed($id_reclamation);

// Fermer ou ouvrir discussion
if (isset($_POST['close'])) {
    $controller->closeDiscussion($id_reclamation);
    header("Location: discussion_admin.php?id=$id_reclamation");
    exit;
}
if (isset($_POST['open'])) {
    $controller->openDiscussion($id_reclamation);
    header("Location: discussion_admin.php?id=$id_reclamation");
    exit;
}

// Supprimer un message uniquement si discussion ouverte
if (isset($_GET['delete_msg']) && !$closed) {
    $controller->deleteMessage((int)$_GET['delete_msg']);
    header("Location: discussion_admin.php?id=$id_reclamation");
    exit;
}

// Envoyer un message uniquement si discussion ouverte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['close'], $_POST['open']) && !$closed) {
    $contenu = trim($_POST['contenu']);
    $fichier = $_FILES['fichier'] ?? null;

    if (!empty($contenu) || ($fichier && $fichier['error'] === UPLOAD_ERR_OK)) {
        $controller->sendMessage($id_reclamation, 'admin', $contenu, $fichier);
        header("Location: discussion_admin.php?id=$id_reclamation");
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
<title>Discussion Admin</title>
<link rel="stylesheet" href="css/chat.css">
<style>
.close-chat {background:red;color:white;padding:5px 10px;margin-left:10px;}
.open-chat {background:green;color:white;padding:5px 10px;margin-left:10px;}
.delete-msg {color:red;text-decoration:none;margin-left:5px;}
</style>
</head>
<body>
<div class="chat-container">
<header class="chat-header">
    <h2>Discussion Réclamation #<?= $id_reclamation ?></h2>
    <span class="chat-status"><?= $closed ? 'Fermée' : 'Ouverte' ?></span>

    <form method="POST" style="display:inline;">
        <?php if(!$closed): ?>
            <button type="submit" name="close" class="close-chat">Fermer discussion</button>
        <?php else: ?>
            <button type="submit" name="open" class="open-chat">Ouvrir discussion</button>
        <?php endif; ?>
    </form>
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
            <?php if(!$closed): ?>
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
<p class="closed">La discussion est fermée. L’utilisateur ne peut plus répondre.</p>
<?php endif; ?>

<a href="listReclamations.php" class="back">← Retour aux Réclamations</a>
</div>
<script src="public/js/chat.js"></script>
</body>
</html>
