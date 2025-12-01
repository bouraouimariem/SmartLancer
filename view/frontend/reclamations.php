<?php
session_start();
require_once __DIR__ . "/../../Model/Reclamation.php";

$model = new Reclamation();
$userEmail = $_SESSION['email'] ?? null;
$reclamations = $model->listReclamations($userEmail);

if (isset($_GET['delete_id'])) {
    $id_reclamation = (int)$_GET['delete_id'];
    $model->deleteReclamation($id_reclamation);
    header("Location: reclamations.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Réclamations</title>
    <link rel="stylesheet" href="css/listreclamations.css">
</head>
<body>
<div class="container">
    <h1>Liste de mes réclamations</h1>

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
                <a href="discussion_user.php?id=<?= $rec['id_reclamation'] ?>" class="button chat">Voir discussion</a>

                
 
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
