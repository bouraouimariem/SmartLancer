<?php
require_once "../../Controller/ReclamationController.php";
$controller = new ReclamationController();

$responses = $controller->getAllResponses();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste des Réponses</title>
<link rel="stylesheet" href="css/listreponses.css">
</head>
<body>
<div class="container">
<h1>Liste des Réponses</h1>

<table>
<tr>
    <th>ID</th>
    <th>Réclamation</th>
    <th>Message</th>
    <th>Date</th>
    <th>Actions</th>
</tr>

<?php foreach($responses as $res): ?>
<?php $closed = $controller->isClosed($res['id_reclamation']); ?>
<tr>
    <td><?= $res['id_reponse'] ?></td>
    <td><?= $res['id_reclamation'] ?></td>
    <td><?= htmlspecialchars($res['contenu']) ?></td>
    <td><?= htmlspecialchars($res['date_reponse']) ?></td>
    <td>
        <?php if (!$closed): ?>
            <a href="updateReponse.php?id=<?= $res['id_reponse'] ?>" class="button update">Modifier</a>
        <?php endif; ?>
        <a href="deleteReponse.php?id=<?= $res['id_reponse'] ?>" class="button delete" onclick="return confirm('Supprimer cette réponse ?');">Supprimer</a>
        <a href="discussion_admin.php?id=<?= $res['id_reclamation'] ?>" class="button view">Ouvrir discussion</a>
    </td>
</tr>
<?php endforeach; ?>

</table>
<br>
<a href="admin_home.php" class="button back"><i class="fas fa-home"></i> Accueil</a>
<a href="listReclamations.php" class="button back"><i class="fas fa-home"></i> Liste Réclamations</a>
</div>
</body>
</html>
