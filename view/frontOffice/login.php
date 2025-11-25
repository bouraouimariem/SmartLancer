<?php
require_once "../../config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = config::getConnexion();
    $query = $db->prepare("SELECT * FROM user WHERE Email = :email");
    $query->execute(['email' => $email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['Password'])) {
        // Auth OK : on enregistre en session puis on redirige vers la page d'animation
        $_SESSION['user'] = $user;
        $_SESSION['id_user'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['Role'];

        header("Location: loading.php");
        exit();
    } else {
        // Auth échouée
        echo "<script>alert('Email ou mot de passe incorrect !');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 40px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6c63ff, #836fff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            width: 350px;
        }
        h2 { text-align: center; color: #6c63ff; margin-bottom: 25px; }
        input {
            width: 100%; padding: 10px; margin: 8px 0 15px; border: 1px solid #ccc; border-radius: 10px;
        }
        button {
            width: 100%; background: #6c63ff; color: white; border: none; padding: 12px; border-radius: 10px;
            cursor: pointer; font-size: 16px;
        }
        button:hover { background: #5848e2; }
        .register-link { text-align: center; margin-top: 10px; }
        .register-link a { color: #6c63ff; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<header>
    <a href="index.php">
        <img src="img/logo.png" alt="logo" width="60">
    </a>
</header>

<div class="login-box">
    <h2>Connexion</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Adresse e-mail" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
    <div class="register-link">
        <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
    </div>
</div>

</body>
</html>
