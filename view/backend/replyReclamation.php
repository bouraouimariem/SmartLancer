<?php
require_once "../../Controller/ReclamationController.php";

$controller = new ReclamationController();
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Aucune réclamation spécifiée.");
}

// Récupération
$reclamation = $controller->get($id);
if (!$reclamation) {
    die("Réclamation introuvable !");
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $response = trim($_POST['response']);

    // Envoyer réponse
    $controller->reply($id, $response);

    // Vider la variable locale pour ne pas réafficher la réponse
    $reclamation['reponse'] = "";

    // Redirection directe
    header("Location: listReclamations.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Répondre à la Réclamation</title>
<link rel="stylesheet" href="style-response.css">
</head>
<body>

<div class="container">

    <h1>Répondre à la Réclamation</h1>

    <!-- -------- INFO RÉCLAMATION -------- -->
    <div class="reclamation-info">
        <p><strong>Nom :</strong> <?= htmlspecialchars($reclamation['nom']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($reclamation['email']) ?></p>
        <p><strong>Sujet :</strong> <?= htmlspecialchars($reclamation['sujet']) ?></p>
        <p><strong>Message :</strong> <?= nl2br(htmlspecialchars($reclamation['message'])) ?></p>
        <p><strong>Date :</strong> <?= htmlspecialchars($reclamation['date_envoi']) ?></p>

        <?php if (!empty($reclamation['reponse'])): ?>
            <p><strong>Réponse existante :</strong><br><?= nl2br(htmlspecialchars($reclamation['reponse'])) ?></p>
        <?php endif; ?>
    </div>

    <!-- -------- FORMULAIRE RÉPONSE -------- -->
    <form method="POST">

        <textarea name="response" id="response" rows="6" required placeholder="Écrire votre réponse ici..."><?= isset($reclamation['reponse']) ? htmlspecialchars($reclamation['reponse']) : '' ?></textarea>

        <div class="actions">
            <button type="submit" class="btn-send">Envoyer la réponse</button>
            <a href="listReclamations.php" class="btn-cancel">Annuler</a>
        </div>
    </form>
</div>

<footer>
    &copy; <?= date('Y') ?> SmartLancer | Tous droits réservés
</footer>

</body>
</html>
