<?php
session_start();
require_once __DIR__ . "/../../Controller/ReclamationController.php";

$controller = new ReclamationController();

// Récupérer l'ID depuis GET
$id_reclamation = $_GET['id'] ?? $_GET['id_reclamation'] ?? null;
if (!$id_reclamation) {
    die("ID de réclamation invalide.");
}

// Récupération des infos
$reclamation = $controller->get((int)$id_reclamation);
$responses = $controller->getResponsesByReclamation((int)$id_reclamation);

if (!$reclamation) {
    die("Réclamation introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réponses - Réclamation #<?= $id_reclamation ?></title>

    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f0f5ff; }
        .container { width: 80%; margin: 40px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        h2 { color: #004c99; }
        .rec-box { background: #e6f0ff; padding: 15px; border-left: 4px solid #005ce6; margin-bottom: 25px; border-radius: 5px; }
        .response { background: #f7faff; padding: 12px; margin-bottom: 12px; border-left: 4px solid #0099ff; border-radius: 5px; }
        .btn { padding: 10px 18px; background: #0066cc; color: white; text-decoration: none; border-radius: 6px; transition: 0.2s; }
        .btn:hover { background: #004a99; }
        .btn2 { padding: 10px 18px; background: #00aaff; color: white; text-decoration: none; border-radius: 6px; transition: 0.2s; }
        .btn2:hover { background: #0088cc; }
    </style>
</head>

<body>
<div class="container">
    <h2>📄 Réclamation #<?= $id_reclamation ?></h2>

    <div class="rec-box">
        <strong>Sujet :</strong> <?= htmlspecialchars($reclamation['sujet']); ?><br>
        <strong>Message :</strong> <?= nl2br(htmlspecialchars($reclamation['message'])); ?><br>
        <strong>Status :</strong> <?= $reclamation['status']; ?><br>
        <strong>Date :</strong> <?= $reclamation['date_envoi']; ?>
    </div>

    <h3>💬 Liste des réponses</h3>
    <?php if (empty($responses)) : ?>
        <p>Aucune réponse pour le moment.</p>
    <?php else : ?>
        <?php foreach ($responses as $r) : ?>
            <div class="response">
                <strong>Réponse :</strong><br>
                <?= nl2br(htmlspecialchars($r['contenu'])); ?><br>
                <small><i>Date : <?= $r['date_reponse']; ?></i></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <br><br>

    <!-- 🔵 bouton Discussion -->
    <a href="discussion_user.php?id=<?= $id_reclamation ?>" class="btn2">
        💬 Ouvrir la discussion
    </a>

    <!-- 🔙 retour -->
    <a href="reclamations.php" class="btn" style="margin-left:10px;">⬅ Retour</a>
</div>
</body>
</html>
