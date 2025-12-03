<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #ffffff; /* fond blanc */
            overflow: hidden;
            position: relative;
        }

        /* Arrière-plan animé bleu clair */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 30%, rgba(0,123,255,0.1) 0%, transparent 70%), 
                        radial-gradient(circle at 70% 70%, rgba(0,123,255,0.1) 0%, transparent 70%);
            background-size: 100% 100%;
            animation: bgMove 25s linear infinite;
            z-index: 0;
        }

        @keyframes bgMove {
            0% { transform: translate(0,0) rotate(0deg); }
            100% { transform: translate(50px,50px) rotate(360deg); }
        }

        .container {
            position: relative;
            z-index: 1;
            background: #ffffffee; /* carte semi-transparente */
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            padding: 50px 40px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            transform: translateY(-50px);
            animation: fadeIn 1s forwards;
        }

        @keyframes fadeIn {
            to { transform: translateY(0); opacity: 1; }
        }

        .logo {
            width: 120px;
            margin-bottom: 25px;
            transition: transform 0.3s;
        }

        .logo:hover {
            transform: rotate(-10deg) scale(1.05);
        }

        h1 {
            margin-bottom: 40px;
            color: #1e3c72;
            font-size: 26px;
            font-weight: 600;
        }

        .btn-box {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .admin-btn {
            padding: 15px 25px;
            border-radius: 12px;
            text-decoration: none;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .admin-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: rgba(255,255,255,0.2);
            transform: skewX(-20deg);
            transition: 0.5s;
        }

        .admin-btn:hover::before {
            left: 200%;
        }

        .admin-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(0,0,0,0.3);
        }

        footer {
            margin-top: 30px;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <img src="css/logo.png" alt="Logo SmartLancer" class="logo">
    <h1>Bienvenue Administrateur</h1>

    <div class="btn-box">
        <a href="listReclamations.php" class="admin-btn">Réclamations</a>
        <a href="listReponses.php" class="admin-btn">Réponses</a>
    </div>

    <footer>
        &copy; <?= date('Y') ?> SmartLancer | Administration
    </footer>
</div>

</body>
</html>
