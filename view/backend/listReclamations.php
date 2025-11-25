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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<!-- CSS Admin -->
<style>
/* --------- RESET --------- */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* --------- BODY --------- */
body {
    background: #d6f5d6;
    padding: 20px;
}

/* --------- CONTAINER --------- */
.container {
    max-width: 1200px;
    margin: auto;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(8px);
    padding: 30px 20px;
    border-radius: 20px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
}

/* --------- TITRE --------- */
h1 {
    text-align: center;
    font-size: 30px;
    font-weight: bold;
    color: #1a3d1a;
    margin-bottom: 30px;
}

/* --------- TABLEAU --------- */
.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

th, td {
    padding: 14px 12px;
    text-align: left;
}

th {
    background-color: #2e8b57;
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
}

tr:nth-child(even) {
    background-color: #f0fff0;
}

tr:hover {
    background-color: #e6ffe6;
    transition: 0.2s;
}

/* --------- BOUTONS --------- */
.button {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    color: #fff;
    font-weight: bold;
    font-size: 14px;
    transition: all 0.3s ease;
}

/* Couleurs boutons */
.delete {
    background-color: #e74c3c;
}

.delete:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
}

.reply {
    background-color: #3498db;
}

.reply:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
}

/* --------- STATUT --------- */
.status {
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 4px;
    color: white;
}

.status-en-attente {
    background-color: orange;
}

.status-repondu {
    background-color: green;
}

.status-clos {
    background-color: red;
}

/* --------- FOOTER --------- */
footer {
    text-align: center;
    margin-top: 30px;
    color: #1a3d1a;
    font-weight: bold;
}

/* --------- RESPONSIVE --------- */
@media (max-width: 768px) {
    table {
        font-size: 13px;
    }

    th, td {
        padding: 10px 8px;
    }

    .button {
        width: 100%;
        justify-content: center;
        margin-bottom: 5px;
    }
}
.button.back {
    background-color: #1a3d1a; /* vert foncé */
}

.button.back:hover {
    background-color: #13301a;
    transform: translateY(-2px);
}

</style>
</head>
<body>

<div class="container">
    <h1>Liste des Réclamations - Administration</h1>

    <div class="table-wrapper">
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
                    $statusClass = match(strtolower($rec['status'])) {
                        'en attente' => 'status-en-attente',
                        'répondu' => 'status-repondu',
                        'clos' => 'status-clos',
                        default => ''
                    };
                ?>
                <span class="status <?= $statusClass ?>"><?= htmlspecialchars($rec['status']) ?></span>
            </td>
            <td>
                <a href="../../Controller/ReclamationController.php?action=delete&id=<?= $rec['id'] ?>" 
                   class="button delete"
                   onclick="return confirm('Voulez-vous vraiment supprimer cette réclamation ?');">
                   Supprimer
                </a>

                <a href="replyReclamation.php?id=<?= $rec['id'] ?>" class="button reply">
                    <?= empty($rec['reponse']) ? 'Répondre' : 'Modifier' ?>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
        <div class="back-zone">
           <a href="admin_home.php" class="button back"><i class="fas fa-home"></i> Accueil</a>
        </div>

    <footer>
        &copy; <?= date('Y') ?> SmartLancer | Tous droits réservés
    </footer>
</div>

</body>
</html>
