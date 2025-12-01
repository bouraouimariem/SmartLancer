<?php
require_once __DIR__ . '/../../Controller/ReclamationController.php';

$controller = new ReclamationController();
$reclamations = $controller->listWithResponses(); // récupère toutes les réclamations avec réponses
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Réclamations</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0fff0; padding: 20px; }
        h2 { text-align: center; color: #1a3d1a; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; vertical-align: top; }
        th { background-color: #2e8b57; color: white; }
        tr:nth-child(even) { background-color: #f9fff9; }
        tr:hover { background-color: #e6ffe6; }
        .reponse { background-color: #d6f5d6; padding: 8px; border-radius: 6px; margin-top: 5px; }
        .button { padding: 5px 10px; text-decoration: none; color: white; border-radius: 5px; font-size: 14px; }
        .view { background-color: #3498db; }
        .view:hover { background-color: #2980b9; }
    </style>
</head>
<body>

<h2>Mes Réclamations et Réponses</h2>

<?php if (empty($reclamations)): ?>
    <p style="text-align:center;">Il n'y a encore aucune réclamation.</p>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Sujet</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Message</th>
            <th>Statut</th>
            <th>Date d'envoi</th>
            <th>Réponse</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($reclamations as $rec): ?>
        <tr>
            <td><?= htmlspecialchars($rec['ID_reclamation']) ?></td>
            <td><?= htmlspecialchars($rec['sujet']) ?></td>
            <td><?= htmlspecialchars($rec['nom']) ?></td>
            <td><?= htmlspecialchars($rec['email']) ?></td>
            <td><?= htmlspecialchars($rec['telephone']) ?></td>
            <td><?= nl2br(htmlspecialchars($rec['message'])) ?></td>
            <td><?= htmlspecialchars($rec['status']) ?></td>
            <td><?= $rec['date_envoi'] ?></td>
            <td>
                <?php if ($rec['ID_reponse']): ?>
                    <div class="reponse">
                        <?= nl2br(htmlspecialchars($rec['contenu'])) ?><br>
                        <small><strong>Date :</strong> <?= $rec['date_reponse'] ?></small>
                    </div>
                <?php else: ?>
                    <em>Pas encore de réponse.</em>
                <?php endif; ?>
            </td>
            
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>
