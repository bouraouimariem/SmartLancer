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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body{background:#d6f5d6;padding:20px;font-family:Arial;}
.container{max-width:1200px;margin:auto;background:rgba(255,255,255,0.9);padding:30px 20px;border-radius:20px;box-shadow:0 12px 35px rgba(0,0,0,0.15);}
h1{text-align:center;font-size:30px;color:#1a3d1a;margin-bottom:30px;}
table{width:100%;border-collapse:collapse;}
th,td{padding:12px;text-align:left;}
th{background:#2e8b57;color:#fff;}
tr:nth-child(even){background:#f0fff0;}
tr:hover{background:#e6ffe6;transition:0.2s;}
.button{padding:6px 12px;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;}
.delete{background:#e74c3c;} .delete:hover{background:#c0392b;}
.update{background:#f39c12;} .update:hover{background:#d68910;}
.view{background:#3498db;} .view:hover{background:#2980b9;}
.back{background:#1a3d1a;} .back:hover{background:#13301a;}
</style>
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
<tr>
    <td><?= $res['id_reponse'] ?></td>
    <td><?= $res['id_reclamation'] ?></td>
    <td><?= htmlspecialchars($res['contenu']) ?></td>
    <td><?= htmlspecialchars($res['date_reponse']) ?></td>
    <td>
        <a href="updateReponse.php?id=<?= $res['id_reponse'] ?>" class="button update">Modifier</a>
        <a href="deleteReponse.php?id=<?= $res['id_reponse'] ?>" class="button delete" onclick="return confirm('Supprimer cette réponse ?');">Supprimer</a>
        <a href="discussion_admin.php?id=<?= $res['id_reclamation'] ?>" class="button view">Ouvrir discussion</a>
    </td>
</tr>
<?php endforeach; ?>

</table>
<br>
<a href="admin_home.php" class="button back"><i class="fas fa-home"></i> Accueil</a>
</div>
</body>
</html>
