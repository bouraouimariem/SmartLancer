<?php
require_once "../../Controller/ReclamationController.php";
$controller = new ReclamationController();
$reclamations = $controller->list();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste Réclamations</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body{background:#d6f5d6;padding:20px;font-family:Arial;}
.container{max-width:1200px;margin:auto;background:rgba(255,255,255,0.9);backdrop-filter:blur(8px);padding:30px 20px;border-radius:20px;box-shadow:0 12px 35px rgba(0,0,0,0.15);}
h1{text-align:center;font-size:30px;color:#1a3d1a;margin-bottom:30px;}
table{width:100%;border-collapse:collapse;}
th,td{padding:12px;text-align:left;}
th{background:#2e8b57;color:#fff;}
tr:nth-child(even){background:#f0fff0;}
tr:hover{background:#e6ffe6;transition:0.2s;}
.button{padding:6px 12px;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;}
.delete{background:#e74c3c;} .delete:hover{background:#c0392b;}
.reply{background:#3498db;} .reply:hover{background:#2980b9;}
.update{background:#f39c12;} .update:hover{background:#d68910;}
.back{background:#1a3d1a;} .back:hover{background:#13301a;}
.status-en-attente{background:orange;color:white;padding:2px 6px;border-radius:4px;}
.status-repondu{background:green;color:white;padding:2px 6px;border-radius:4px;}
.status-clos{background:red;color:white;padding:2px 6px;border-radius:4px;}
</style>
</head>
<body>
<div class="container">
<h1>Liste des Réclamations</h1>
<table>
<tr><th>ID</th><th>Nom</th><th>Email</th><th>Sujet</th><th>Message</th><th>Date</th><th>telephone</th><th>Statut</th><th>Actions</th></tr>
<?php foreach($reclamations as $rec): ?>
<tr>
<td><?= $rec['id_reclamation'] ?></td>
<td><?= htmlspecialchars($rec['nom']) ?></td>
<td><?= htmlspecialchars($rec['email']) ?></td>
<td><?= htmlspecialchars($rec['sujet']) ?></td>
<td><?= htmlspecialchars($rec['message']) ?></td>
<td><?= htmlspecialchars($rec['telephone']) ?></td>
<td><?= $rec['date_envoi'] ?></td>
<td>
<span class="<?= strtolower($rec['status'])=='répondu'?'status-repondu':(strtolower($rec['status'])=='clos'?'status-clos':'status-en-attente') ?>">
<?= htmlspecialchars($rec['status']) ?></span>
</td>
<td>
<a href="deleteReclamation.php?id_reclamation=<?= $rec['id_reclamation'] ?>" class="button delete" onclick="return confirm('Supprimer ?');">Supprimer</a>
<!-- Répondre uniquement si la réclamation est en attente -->
    <?php if (strtolower($rec['status']) === 'en attente'): ?>
        <a href="replyReclamation.php?id=<?= $rec['id_reclamation'] ?>" class="button reply">Répondre</a>
    <?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
<br>
<a href="admin_home.php" class="button back"><i class="fas fa-home"></i> Accueil</a>
</div>
</body>
</html>
