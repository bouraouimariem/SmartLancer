<?php
session_start();

// Sécurité : si l’utilisateur n’est pas connecté → retour login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Détermine la destination selon le rôle
$redirect = "login.php";

if ($_SESSION['role'] === "admin") {
    $redirect = "../backOffice/index.php";
} elseif ($_SESSION['role'] === "client") {
    $redirect = "pages/accueil_client.php";
} elseif ($_SESSION['role'] === "freelance") {
    $redirect = "pages/accueil_freelancer.php";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Chargement...</title>

<style>
    body {
        margin: 0;
        background: #024b17ff;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        color: #00ffcc;
        font-family: Arial, sans-serif;
    }

    .neon {
  font-size: 100px;
  color: #0aff0a;
  font-weight: bold;
  font-family: Arial, sans-serif;
  text-shadow: 
    0 0 5px #0aff0a,
    0 0 10px #0aff0a,
    0 0 20px #0aff0a,
    0 0 40px #0aff0a;
  animation: glow 1s infinite alternate;
}

@keyframes glow {
  0% {
    text-shadow: 
      0 0 5px #0aff0a,
      0 0 10px #0aff0a;
  }
  100% {
    text-shadow: 
      0 0 5px #00ff00,
      0 0 50px #00ff00,
      0 0 100px #00ff00;
  }
}

</style>

<script>
// Redirection automatique après 3 secondes
setTimeout(() => {
    window.location.href = "<?= $redirect ?>";
}, 3000);
</script>

</head>
<body>
    <div class="neon">SmartLancer</div>
</body>
</html>
        