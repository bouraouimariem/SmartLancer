<?php
session_start();
require_once __DIR__ . "/../../Controller/ReclamationController.php";

$controller = new ReclamationController();

// Récupérer les filtres depuis l'URL
$filters = [
    'status' => $_GET['status'] ?? null,
    'date' => $_GET['date'] ?? null,
    'search' => $_GET['search'] ?? null,
    'nom' => $_GET['nom'] ?? null,
    'email' => $_GET['email'] ?? null
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
                <option value="Fermée" <?= (isset($_GET['status']) && $_GET['status']=='Fermée') ? 'selected' : '' ?>>Fermée</option>

            </select>
        </label>

        <label>Date: <input type="date" name="date" value="<?= $_GET['date'] ?? '' ?>"></label>
        <label>Recherche: <input type="text" name="search" placeholder="ID, Nom ou Email" value="<?= $_GET['search'] ?? '' ?>"></label>

        <label>Nom: <input type="text" name="nom" value="<?= $_GET['nom'] ?? '' ?>"></label>
        <label>Email: <input type="email" name="email" value="<?= $_GET['email'] ?? '' ?>"></label>


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
                    <td>
                    <span class="<?= 
                        strtolower($rec['status'])=='répondu' ? 'status-repondu' : 
                        (in_array(strtolower($rec['status']), ['clos','fermé','fermée']) ? 'status-clos' : 'status-en-attente') 
                    ?>">
                        <?= in_array(strtolower($rec['status']), ['clos','fermé','fermée']) ? 'Fermée' : htmlspecialchars($rec['status']) ?>
                    </span>
                    </td>

                  <td>
                    <?php if(strtolower($rec['status']) == 'en attente'): ?>
                        <a href="index.php?edit_id=<?= $rec['id_reclamation'] ?>" class="button edit">Modifier</a>
                    <?php endif; ?>

                    <a href="reclamations.php?delete_id=<?= $rec['id_reclamation'] ?>" class="button delete"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?');">Supprimer</a>

                    <?php if(strtolower($rec['status']) != 'en attente'): ?>
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
