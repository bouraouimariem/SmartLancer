<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartLancer - Accueil</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: #f7f9fc;
            color: #333;
            line-height: 1.6;
        }

        /* Header */
        header {
            background: linear-gradient(90deg, #1E90FF, #187bcd);
            color: white;
            padding: 25px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        header h1 {
            font-size: 42px;
            animation: fadeInDown 1s ease-out;
        }

        /* Landing Section */
        .landing-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 20px;
            background: url('background.jpg') no-repeat center center/cover;
            color: white;
            min-height: 80vh;
            position: relative;
        }

        .landing-container::after {
            content: '';
            position: absolute;
            inset: 0;
            background-color: rgba(0,0,0,0.4);
        }

        .landing-content {
            position: relative;
            z-index: 1;
        }

        .landing-container .logo {
            width: 150px;
            margin-bottom: 20px;
            animation: bounce 1.5s infinite alternate;
        }

        .landing-container h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .landing-container .subtitle {
            font-size: 22px;
            margin-bottom: 30px;
        }

        .button-group a {
            display: inline-block;
            margin: 10px;
            padding: 15px 35px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        .btn {
            background-color: #ffffff;
            color: #1E90FF;
        }

        .btn:hover {
            background-color: #e0e0e0;
            transform: translateY(-3px);
        }

        .secondary-btn {
            background-color: transparent;
            border: 2px solid white;
            color: white;
        }

        .secondary-btn:hover {
            background-color: white;
            color: #1E90FF;
            transform: translateY(-3px);
        }

        /* Sections d'information */
        section {
            padding: 80px 20px;
            text-align: center;
        }

        section h2 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #1E90FF;
            position: relative;
        }

        section h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: #1E90FF;
            margin: 10px auto 0 auto;
            border-radius: 2px;
        }

        section p {
            font-size: 18px;
            max-width: 900px;
            margin: 20px auto 40px auto;
            color: #555;
        }

        .info-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }

        .card {
            background-color: white;
            border-radius: 15px;
            padding: 30px 20px;
            width: 280px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: 0.4s;
            opacity: 0;
            transform: translateY(50px);
        }

        .card.visible {
            opacity: 1;
            transform: translateY(0);
            transition: 0.6s;
        }

        .card i {
            font-size: 50px;
            color: #1E90FF;
            margin-bottom: 15px;
        }

        .card h3 {
            font-size: 24px;
            margin-bottom: 12px;
        }

        .card p {
            font-size: 16px;
            color: #666;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }

        /* Footer */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 25px 0;
        }

        /* Animations */
        @keyframes fadeInDown {
            0% {opacity: 0; transform: translateY(-50px);}
            100% {opacity: 1; transform: translateY(0);}
        }

        @keyframes bounce {
            0% {transform: translateY(0);}
            100% {transform: translateY(-15px);}
        }

        /* Responsive */
        @media(max-width: 768px) {
            .info-cards {
                flex-direction: column;
                align-items: center;
            }

            .landing-container h1 {
                font-size: 36px;
            }

            .landing-container .subtitle {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>SmartLancer</h1>
</header>

<div class="landing-container">
    <div class="landing-content">
        <img src="logo.png" alt="Logo SmartLancer" class="logo">
        <h1>Bienvenue sur SmartLancer</h1>
        <p class="subtitle">Envoyez vos réclamations et suivez leur statut facilement.</p>
        <div class="button-group">
            <a href="index.php" class="btn">Envoyer une réclamation</a>
            <a href="reclamations.php" class="btn secondary-btn">Mes réclamations</a>
        </div>
    </div>
</div>

<section>
    <h2>Nos Avantages</h2>
    <p>SmartLancer vous permet de gérer vos réclamations rapidement, efficacement et en toute transparence.</p>
    <div class="info-cards">
        <div class="card">
            <i class="fas fa-paper-plane"></i>
            <h3>Envoyer facilement</h3>
            <p>Soumettez vos réclamations depuis n'importe quel appareil en quelques clics.</p>
        </div>
        <div class="card">
            <i class="fas fa-clock"></i>
            <h3>Suivi en temps réel</h3>
            <p>Consultez le statut de vos réclamations et soyez informé à chaque étape.</p>
        </div>
        <div class="card">
            <i class="fas fa-headset"></i>
            <h3>Support rapide</h3>
            <p>Notre équipe répond rapidement pour résoudre vos problèmes efficacement.</p>
        </div>
    </div>
</section>

<section style="background-color:#f0f0f0;">
    <h2>Exemples Réels</h2>
    <p>Découvrez comment SmartLancer a aidé d'autres utilisateurs à résoudre leurs problèmes.</p>
    <div class="info-cards">
        <div class="card">
            <i class="fas fa-bug"></i>
            <h3>Problème technique</h3>
            <p>Correction rapide d'un bug sur l'application mobile.</p>
        </div>
        <div class="card">
            <i class="fas fa-file-alt"></i>
            <h3>Demande administrative</h3>
            <p>Accompagnement efficace pour la validation de documents officiels.</p>
        </div>
        <div class="card">
            <i class="fas fa-comments"></i>
            <h3>Service client</h3>
            <p>Réponse rapide et claire à une réclamation concernant un produit.</p>
        </div>
    </div>
</section>

<footer>
    &copy; 2025 SmartLancer. Tous droits réservés.
</footer>

<script>
    // Animation des cartes au scroll
    const cards = document.querySelectorAll('.card');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.2 });

    cards.forEach(card => observer.observe(card));
</script>

</body>
</html>
