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
<style>
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .button { padding: 5px 10px; margin: 2px; text-decoration: none; color: white; border-radius: 4px; }
    .delete { background-color: #e74c3c; }
    .reply { background-color: #3498db; }
</style>
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
    <th>Statut</th>
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
        <?php
            $color = match($rec['status']) {
                'En attente' => 'orange',
                'Répondu' => 'green',
                'Clos' => 'red',
                default => 'black',
            };
        ?>
        <span style="color:<?= $color ?>; font-weight:bold;"><?= htmlspecialchars($rec['status']) ?></span>
    </td>

    <td>
        <a href="../../Controller/ReclamationController.php?action=delete&id=<?= $rec['id'] ?>" 
           class="button delete"
           onclick="return confirm('Voulez-vous vraiment supprimer cette réclamation ?');">
           Supprimer
        </a>

        <?php if (!empty($rec['reponse'])): ?>
            <a href="replyReclamation.php?id=<?= $rec['id'] ?>" class="button reply">Modifier</a>
        <?php else: ?>
            <a href="replyReclamation.php?id=<?= $rec['id'] ?>" class="button reply">Répondre</a>
        <?php endif; ?>

    </td>
</tr>
<?php endforeach; ?>
</table>

<footer>
    &copy; <?= date('Y') ?> SmartLancer | Tous droits réservés
</footer>

</body>
</html>
