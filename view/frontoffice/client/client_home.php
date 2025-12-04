<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace client</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.9s ease-out forwards;
        }

        /* Hover 3D */
        .btn-3d {
            transition: 0.25s ease;
        }
        .btn-3d:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

<!-- HEADER -->
<header class="flex justify-between items-center bg-green-700 text-white shadow px-6 py-4 fixed w-full top-0 left-0 z-50 fade-in">
    <div class="flex items-center gap-3">
        <img src="/project/uploads/logo.png" alt="logo" class="h-9 w-9 rounded-md">
        <span class="text-xl font-semibold">SmartLancer</span>
    </div>

    <a href="/project/index.php?route=profil"
       class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 shadow btn-3d">
       ⬅ deconnexion
    </a>
    
</header>

<!-- CONTENU -->
<main class="pt-28 px-6 fade-in">
    <div class="bg-white shadow-lg p-10 rounded-2xl max-w-3xl mx-auto text-center">

        

        <!-- BOUTONS -->
        <div class="grid md:grid-cols-3 gap-6 mt-10">

            <a href="#blog"
               class="p-6 bg-blue-600 text-white rounded-xl shadow btn-3d">
                📰 Blog
            </a>

            

            <a href="#projets"
               class="p-6 bg-yellow-500 text-white rounded-xl shadow btn-3d">
                📂 Projets
            </a>

            <a href="#profiles"
               class="p-6 bg-purple-600 text-white rounded-xl shadow btn-3d">
                💬 Voir Profils Freelancers
            </a>

            <a href="#reclamation"
               class="p-6 bg-red-500 text-white rounded-xl shadow btn-3d">
                ⚠️ Réclamations
            </a>

            <a href="#messages"
               class="p-6 bg-gray-700 text-white rounded-xl shadow btn-3d">
                💌 Messagerie
            </a>

        </div>

    </div>
</main>

<!-- FOOTER -->
<footer class="fixed bottom-0 left-0 w-full bg-white shadow-inner py-3 text-center text-gray-600 text-sm fade-in">
    © 2025 Esprit — By Web Creator
</footer>

</body>
</html>
