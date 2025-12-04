<?php
session_start();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenue client</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
/* Fade-in animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade {
    animation: fadeIn 1.2s ease-out forwards;
}
</style>

<!-- Redirection automatique -->
<script>
setTimeout(() => {
    window.location.href = "/project/index.php?route=client";


}, 3000); // 3 secondes
</script>

</head>

<body class="bg-gradient-to-br from-green-600 to-green-900 text-white flex items-center justify-center h-screen">

<div class="text-center fade">
    <h1 class="text-5xl font-bold mb-4">Bienvenue <?= $_SESSION['name']; ?> 👋</h1>
    <p class="text-xl opacity-90 mb-8">Chargement de votre espace client...</p>

    <div class="flex justify-center">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-white"></div>
    </div>
</div>

</body>
</html>
