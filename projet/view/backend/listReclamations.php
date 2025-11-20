<?php
require_once "../../Controller/ReclamationController.php";

$controller = new ReclamationController();
$reclamations = $controller->list();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Administration - Réclamations</title>
<link rel="stylesheet" href="style.css">

</head>
<body>
<h1>Liste des Réclamations</h1>

<table>
<tr>
<th>ID</th>
<th>Nom</th>
<th>Email</th>
<th>Sujet</th>
<th>Message</th>
<th>Date</th>
<th>Actions</th>
</tr>

<?php foreach ($reclamations as $rec): ?>
<tr>
<td><?= $rec['id'] ?></td>
<td><?= htmlspecialchars($rec['nom']) ?></td>
<td><?= htmlspecialchars($rec['email']) ?></td>
<td><?= htmlspecialchars($rec['sujet']) ?></td>
<td><?= htmlspecialchars($rec['message']) ?></td>
<td><?= $rec['date_envoi'] ?></td>
<td>
<a href="../../Controller/ReclamationController.php?action=delete&id=<?= $rec['id'] ?>" class="button delete" 
onclick="return confirm('Voulez-vous vraiment supprimer cette réclamation ?');">Supprimer</a>
<a href="replyReclamation.php?id=<?= $rec['id'] ?>" class="button reply">Répondre</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<footer>
    &copy; <?= date('Y') ?> SmartLancer | Tous droits réservés
</footer>

</body>
</html>
