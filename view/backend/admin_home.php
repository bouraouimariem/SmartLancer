<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord - Admin</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #d6f5d6; /* Vert pastel */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.7);
            padding: 40px 60px;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        h1 {
            color: #1a3d1a;
            margin-bottom: 30px;
        }

        .btn-box {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        a.admin-btn {
            display: block;
            padding: 14px 25px;
            background-color: #2e8b57;
            color: white;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px;
            transition: 0.25s;
        }

        a.admin-btn:hover {
            background-color: #256f47;
            transform: translateY(-3px);
        }

        footer {
            margin-top: 25px;
            font-size: 14px;
            color: #1a3d1a;
        }
    </style>

</head>
<body>

<div class="container">
    <h1>Tableau de Bord Administrateur</h1>

    <div class="btn-box">

        <!-- Réclamations reçues -->
        <a href="listReclamations.php" class="admin-btn">📨 Réclamations Reçues</a>

        <!-- Réponses envoyées -->
        <a href="listReponses.php" class="admin-btn">📤 Réponses Envoyées</a>

    </div>

    <footer>
        &copy; <?= date('Y') ?> SmartLancer | Administration
    </footer>
</div>

</body>
</html>
