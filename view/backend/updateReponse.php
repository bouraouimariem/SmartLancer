<?php
require_once "../../Controller/ReclamationController.php";
$controller = new ReclamationController();

$id = $_GET['id'] ?? null;
if (!$id) die("Réponse introuvable");

$reponse = $controller->getResponse($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu']);
    $controller->updateResponse($id, $contenu);
    header("Location: listReponses.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier la Réponse</title>
<style>
body{background:#d6f5d6;padding:20px;font-family:Arial;}
.container{max-width:600px;margin:auto;background:rgba(255,255,255,0.9);padding:30px;border-radius:20px;box-shadow:0 12px 35px rgba(0,0,0,0.15);}
textarea{width:100%;padding:10px;font-size:16px;border-radius:6px;margin-bottom:15px;}
button{padding:10px 20px;background:#f39c12;color:white;border:none;border-radius:6px;font-weight:bold;cursor:pointer;}
button:hover{background:#d68910;}
.back{margin-top:15px;display:inline-block;text-decoration:none;color:white;background:#1a3d1a;padding:8px 14px;border-radius:6px;}
.back:hover{background:#13301a;}
</style>
</head>
<body>
<div class="container">
<h2>Modifier la Réponse #<?= $id ?></h2>
<form method="POST">
<textarea name="contenu" rows="6" required><?= htmlspecialchars($reponse['contenu']) ?></textarea><br>
<button type="submit">Modifier</button>
</form>
<a href="listReponses.php" class="back">← Retour aux Réponses</a>
</div>
</body>
</html>
