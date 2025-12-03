<?php
require_once "../../Controller/ReclamationController.php";

$controller = new ReclamationController();
$id_reclamation = $_GET['id'] ?? null;
if (!$id_reclamation) die("Réclamation introuvable");

// Récupérer les infos de la réclamation
$reclamation = $controller->get($id_reclamation);
if (!$reclamation) die("Réclamation introuvable");

// Vérifier si la réclamation a déjà une réponse
$responses = $controller->getResponsesByReclamation($id_reclamation);
$deja_repondu = !empty($responses);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$deja_repondu) {
    $contenu = trim($_POST['contenu']);
    if ($controller->reply($id_reclamation, $contenu)) {
        header("Location: listReponses.php");
        exit;
    } else {
        $error = "Le contenu de la réponse ne peut pas être vide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Répondre à la Réclamation</title>
<link rel="stylesheet" href="css/replayReclamation.css">

</head>
<body>
<div class="container">
<h2>Répondre à la Réclamation N°:<?= $id_reclamation ?></h2>

<div class="info">
    <p><strong>Nom:</strong> <?= htmlspecialchars($reclamation['nom']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($reclamation['email']) ?></p>
    <p><strong>Sujet:</strong> <?= htmlspecialchars($reclamation['sujet']) ?></p>
    <p><strong>Message:</strong> <?= htmlspecialchars($reclamation['message']) ?></p>
    <p><strong>Téléphone:</strong> <?= htmlspecialchars($reclamation['telephone']) ?></p>
</div>

<?php if (!$deja_repondu): ?>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <textarea name="contenu" rows="6" required></textarea><br>
        <button type="submit">Envoyer la Réponse</button>
    </form>
<?php else: ?>
    <p class="error">Cette réclamation a déjà été répondue.</p>
<?php endif; ?>

<a href="listReclamations.php" class="back">← Retour aux Réclamations</a>
</div>
<script src="replayReclamation.js"></script>
</body>
</html>
