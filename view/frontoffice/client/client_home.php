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
    <title>Espace Client</title>
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
        .btn-3d:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
        }

        .btn-3d {
            transition: 0.25s ease;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen">

    <!-- Header -->
    <header class="flex justify-between items-center bg-green-700 text-white shadow-lg px-6 py-4 fixed w-full top-0 left-0 z-50 fade-in">

        <span class="text-xl font-bold tracking-wide">🌿 SmartLancer</span>

        <a href="/project/index.php?route=logout"
            class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700 transition btn-3d">
            Déconnexion
        </a>

    </header>

    <!-- Contenu principal -->
    <main class="pt-32 px-6">

        <div class="bg-white shadow-2xl p-10 rounded-2xl max-w-3xl mx-auto text-center fade-in"
             style="animation-delay: 0.3s;">

            <h1 class="text-4xl font-extrabold text-green-700 mb-4 drop-shadow-md">
                Bienvenue, <?= $_SESSION['name']; ?> 👋
            </h1>

            <p class="text-gray-700 text-lg opacity-90">
                Choisissez une action parmi les options ci-dessous.
            </p>
        </div>

    </main>

    <!-- Boutons centrés -->
    <div class="mt-10 flex justify-center items-center gap-6 flex-wrap fade-in"
         style="animation-delay: 0.6s;">

        <a href="#blog"
           class="btn-3d bg-blue-600 text-white px-8 py-4 rounded-xl shadow-lg hover:bg-blue-700 text-lg transition">
            📝 Blog
        </a>

        <a href="/project/index.php?route=all_freelancers"
   class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-800">
   Voir Profils Freelancers
</a>


        <a href="#projets"
           class="btn-3d bg-yellow-600 text-white px-8 py-4 rounded-xl shadow-lg hover:bg-yellow-700 text-lg transition">
            📁 Projets
        </a>

        <a href="#avis"
           class="btn-3d bg-purple-600 text-white px-8 py-4 rounded-xl shadow-lg hover:bg-purple-700 text-lg transition">
            ⭐ Avis
        </a>

        <a href="#reclamation"
           class="btn-3d bg-red-600 text-white px-8 py-4 rounded-xl shadow-lg hover:bg-red-700 text-lg transition">
            ⚠️ Réclamation
        </a>

    </div>

    <footer class="flex justify-center items-center bg-white text-black shadow-md px-6 py-4 w-full fixed bottom-0 left-0 z-50 fade-in"
            style="animation-delay: 0.9s;">
        <div class="text-center">
            <span class="text-sm opacity-80">© 2025 Esprit — By Web Creator</span>
        </div>
    </footer>

</body>
</html>
