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
<style>
body{background:#d6f5d6;padding:20px;font-family:Arial;}
.container{max-width:600px;margin:auto;background:rgba(255,255,255,0.9);padding:30px;border-radius:20px;box-shadow:0 12px 35px rgba(0,0,0,0.15);}
textarea{width:100%;padding:10px;font-size:16px;border-radius:6px;margin-bottom:15px;}
button{padding:10px 20px;background:#2e8b57;color:white;border:none;border-radius:6px;font-weight:bold;cursor:pointer;}
button:hover{background:#256f47;}
.back{margin-top:15px;display:inline-block;text-decoration:none;color:white;background:#1a3d1a;padding:8px 14px;border-radius:6px;}
.back:hover{background:#13301a;}
.info{background:#f0fff0;padding:12px;border-radius:6px;margin-bottom:20px;}
.error{color:red;font-weight:bold;margin-bottom:10px;}
</style>
</head>
<body>
<div class="container">
<h2>Répondre à la Réclamation #<?= $id_reclamation ?></h2>

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
</body>
</html>
