<?php
require_once "../../Controller/ReclamationController.php";
$controller = new ReclamationController();

// Récupérer les filtres depuis l'URL
$filters = [
    'status' => $_GET['status'] ?? null,
    'date' => $_GET['date'] ?? null,
    'search' => $_GET['search'] ?? null
];

// Récupérer les réclamations filtrées
$reclamations = $controller->filterAndSearch($filters);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste Réclamations</title>
<link rel="stylesheet" href="css/listReclmation.css">
</head>
<body>
<div class="container">
<h1>Liste des Réclamations</h1>

<form method="GET">
    <label>Status: 
        <select name="status">
            <option value="">Tous</option>
            <option value="En attente" <?= ($_GET['status'] ?? '')=='En attente'?'selected':'' ?>>En attente</option>
            <option value="Répondu" <?= ($_GET['status'] ?? '')=='Répondu'?'selected':'' ?>>Répondu</option>
            <option value="Fermée" <?= ($_GET['status'] ?? '')=='Fermée'?'selected':'' ?>>Fermée</option>
        </select>
    </label>
    <label>Date: <input type="date" name="date" value="<?= $_GET['date'] ?? '' ?>"></label>
    <label>Recherche: <input type="text" name="search" placeholder="ID" value="<?= $_GET['search'] ?? '' ?>"></label>
    <button type="submit">Filtrer</button>
    <a href="listReclamations.php" style="margin-left:10px;">Réinitialiser</a>
</form>

<table>
<tr>
<th>ID</th><th>Nom</th><th>Email</th><th>Sujet</th><th>Message</th><th>Téléphone</th><th>Date</th><th>Statut</th><th>Actions</th>
</tr>

<?php foreach($reclamations as $rec): ?>
<tr>
<td><?= $rec['id_reclamation'] ?></td>
<td><?= htmlspecialchars($rec['nom']) ?></td>
<td><?= htmlspecialchars($rec['email']) ?></td>
<td><?= htmlspecialchars($rec['sujet']) ?></td>
<td><?= htmlspecialchars($rec['message']) ?></td>
<td><?= htmlspecialchars($rec['telephone']) ?></td>
<td><?= htmlspecialchars($rec['date_envoi']) ?></td>
<td>
<span class="<?= strtolower($rec['status'])=='répondu'?'status-repondu':(strtolower($rec['status'])=='fermée'?'status-clos':'status-en-attente') ?>">
<?= htmlspecialchars($rec['status']) ?></span>
</td>
<td>
    <a href="deleteReclamation.php?id=<?= $rec['id_reclamation'] ?>" class="button delete" onclick="return confirm('Supprimer ?');">Supprimer</a>
    <?php if (strtolower($rec['status']) === 'en attente'): ?>
        <a href="replyReclamation.php?id=<?= $rec['id_reclamation'] ?>" class="button reply">Répondre</a>
    <?php endif; ?>
    <a href="discussion_admin.php?id=<?= $rec['id_reclamation'] ?>" class="button view">Ouvrir discussion</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<br>
<a href="admin_home.php" class="button back">Accueil</a>
</div>
</body>
</html>
