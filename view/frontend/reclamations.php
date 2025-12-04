<?php
session_start();
require_once __DIR__ . "/../../Controller/ReclamationController.php";

$controller = new ReclamationController();

// Récupérer les filtres depuis l'URL
$filters = [
    'status' => $_GET['status'] ?? null,
    'date' => $_GET['date'] ?? null,
    'search' => $_GET['search'] ?? null
];

// Récupérer les réclamations filtrées (sans filtrer par email)
$reclamations = $controller->filterAndSearch($filters, 'date_envoi DESC');

if (isset($_GET['delete_id'])) {
    $id_reclamation = (int)$_GET['delete_id'];
    $controller->delete($id_reclamation);
    header("Location: reclamations.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste de réclamations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/listreclamations.css">
    <style>
        form { margin-bottom: 20px; }
        label { margin-right: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Liste des réclamations</h1>

    <!-- FORMULAIRE DE FILTRAGE -->
    <form method="GET">
        <label>Status:
            <select name="status">
                <option value="">Tous</option>
                <option value="En attente" <?= (isset($_GET['status']) && $_GET['status']=='En attente') ? 'selected' : '' ?>>En attente</option>
                <option value="Répondu" <?= (isset($_GET['status']) && $_GET['status']=='Répondu') ? 'selected' : '' ?>>Répondu</option>
                <option value="Clos" <?= (isset($_GET['status']) && $_GET['status']=='Clos') ? 'selected' : '' ?>>fermé</option>
            </select>
        </label>

        <label>Date: <input type="date" name="date" value="<?= $_GET['date'] ?? '' ?>"></label>
        <label>Recherche: <input type="text" name="search" placeholder="ID ou Nom" value="<?= $_GET['search'] ?? '' ?>"></label>

        <button type="submit">Filtrer</button>
        <a href="reclamations.php" style="margin-left:10px;">Réinitialiser</a>
    </form>

    <?php if(empty($reclamations)): ?>
        <p>Aucune réclamation trouvée.</p>
    <?php else: ?>
        <table border="0" cellspacing="0" cellpadding="6">
            <tr style="background:#2f4f2f; color:white;">
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Sujet</th>
                <th>Message</th>
                <th>Téléphone</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php foreach($reclamations as $rec): ?>
                <tr>
                    <td><?= htmlspecialchars($rec['id_reclamation']) ?></td>
                    <td><?= htmlspecialchars($rec['nom']) ?></td>
                    <td><?= htmlspecialchars($rec['email']) ?></td>
                    <td><?= htmlspecialchars($rec['sujet']) ?></td>
                    <td><?= nl2br(htmlspecialchars($rec['message'])) ?></td>
                    <td><?= htmlspecialchars($rec['telephone']) ?></td>
                    <td><?= htmlspecialchars($rec['date_envoi']) ?></td>
                    <td><?= htmlspecialchars($rec['status'] ?? 'En attente') ?></td>
                    <td>
                        <a href="index.php?edit_id=<?= $rec['id_reclamation'] ?>" class="button edit">Modifier</a>
                        <a href="reclamations.php?delete_id=<?= $rec['id_reclamation'] ?>" class="button delete"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?');">Supprimer</a>
                        <?php if($rec['status'] === 'Répondu'): ?>
                            <a href="voir_reponse.php?id=<?= $rec['id_reclamation'] ?>" class="button response">Voir réponse</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="back-zone">
        <a href="index.php" class="button back">Déposer une nouvelle réclamation</a>
        <a href="landing.php" class="button add-new">Accueil</a>
    </div>
</div>
</body>
</html>
