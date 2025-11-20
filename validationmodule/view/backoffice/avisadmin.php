<?php 
require_once __DIR__ . '/../../model/database.php';
require_once __DIR__ . '/../../model/avis.php';

$pdo = (new Database())->getConnection();
$avisModel = new Avis($pdo);

// Supprimer un avis
if (isset($_GET['delete_id'])) {
    $avisModel->deleteAvis($_GET['delete_id']);
    header("Location: avisadmin.php");
    exit;
}

$allAvis = $avisModel->getAllAvis();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - Gestion des Avis</title>

<style>
    body {
        font-family: "Poppins", Arial, sans-serif;
        background-color: #f3f6fb;
        margin: 0;
        padding: 0;
        color: #333;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 240px;
        height: 100%;
        background-color: #1b7c3d;
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 30px;
    }

    .sidebar h2 {
        font-size: 22px;
        margin-bottom: 40px;
        color: #fff;
    }

    .sidebar img.logo {
        height: 100px;
    }

    .sidebar a {
        text-decoration: none;
        color: #fff;
        padding: 12px 20px;
        width: 80%;
        text-align: center;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background-color: #009879;
    }

    header {
        margin-left: 240px;
        background-color: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    header h1 {
        color: #1b7c3d;
        font-size: 24px;
    }

    main {
        margin-left: 240px;
        padding: 40px;
    }

    .card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 20px;
    }

    .card h2 {
        margin-bottom: 20px;
        color: #009879;
        font-size: 22px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        text-align: left;
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
    }

    th {
        background-color: #1b7c3d;
        color: #fff;
    }

    tr:hover {
        background-color: #f1f8f5;
    }

    .note {
        color: #ffc107;
        font-size: 18px;
    }

    .btn-delete {
        background-color: #dc2626;
        color: #fff;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
    }

    .btn-delete:hover {
        background-color: #b91c1c;
    }
</style>
</head>

<body>

    <div class="sidebar">
        <img src="/validationmodule/view/images/logo.png" alt="Logo SmartLancer" class="logo">
        <h2>SmartLancer</h2>

        <a href="#" class="active">Avis & Évaluations</a>
        <a href="#">Utilisateurs</a>
        <a href="#">Projets</a>
        <a href="#">Statistiques</a>
        <a href="#">Déconnexion</a>
    </div>

    <header>
        <h1>Gestion des Avis</h1>
        <input type="search" placeholder="Rechercher un avis...">
    </header>

    <main>
        <div class="card">
            <h2>Liste des avis récents</h2>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Note</th>
                        <th>Likes</th>
                        <th>Supprimer</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($allAvis) > 0): ?>
                        <?php foreach ($allAvis as $avis): ?>
                            <tr>
                                <td><?= htmlspecialchars($avis['nom']) ?></td>
                                <td><?= htmlspecialchars($avis['email']) ?></td>

                                <td class="note">
                                    <?= str_repeat('★', $avis['note']) . str_repeat('☆', 5 - $avis['note']) ?>
                                </td>

                                <td>
                                    ❤️ <?= $avisModel->getLikesCount($avis['id']); ?>
                                </td>

                                <td>
                                    <a href="avisadmin.php?delete_id=<?= $avis['id'] ?>" 
                                       class="btn-delete"
                                       onclick="return confirm('Voulez-vous vraiment supprimer cet avis ?')">
                                       Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">Aucun avis trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </main>

</body>
</html>
