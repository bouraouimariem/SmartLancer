<?php
session_start();
require_once "../../config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['nom']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['role'])) {
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        try {
            $db = config::getConnexion();
            $sql = "INSERT INTO user (nom, Email, Password, Role, created_at) 
                    VALUES (:nom, :email, :password, :role, NOW())";
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $nom,
                'email' => $email,
                'password' => $password,
                'role' => $role
            ]);

            // ✅ Récupération de l'ID auto-incrémenté
            $id_user = $db->lastInsertId();

            // ✅ Sauvegarde des infos dans la session
            $_SESSION['id_user'] = $id_user;
            $_SESSION['nom'] = $nom;
            $_SESSION['role'] = $role;

            // ✅ Redirection selon le rôle
            if ($role == "freelance") {
                header("Location: ../frontOffice/pages/projet_freelancer.php");
            } elseif ($role == "client") {
                header("Location: ../frontOffice/pages/projet_client.php");
            } else {
                header("Location: ../backOffice/pages/dashboard.php");
            }
            exit();

        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Veuillez remplir tous les champs.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte</title>
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

        header img {
            
            cursor: pointer;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6c63ff, #836fff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            width: 350px;
        }
        h2 {
            text-align: center;
            color: #6c63ff;
            margin-bottom: 25px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
        }
        button {
            width: 100%;
            background: #6c63ff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #5848e2;
        }
        .login-link {
            text-align: center;
            margin-top: 10px;
        }
        .login-link a {
            color: #6c63ff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php">
            <img src="img/logo.png" alt="logo" width="60">
        </a>
    </header>
    
    <div class="register-box">
        <h2>Créer un compte</h2>
        <form method="POST">
            <input type="text" name="nom" placeholder="Nom complet" required>
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <select name="role" required>
                <option value="">-- Choisir un rôle --</option>
                <option value="admin">Admin</option>
                <option value="freelance">Freelance</option>
                <option value="client">Client</option>
            </select>
            <button type="submit">S'inscrire</button>
        </form>
        <div class="login-link">
            <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</body>
</html>
