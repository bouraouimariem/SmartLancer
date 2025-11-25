<?php


if (!isset($_SESSION['email']) || $_SESSION['role'] !== "Freelancer") {
    header("Location: ../../index.php?route=login");
    exit();
}

// Portfolio envoyé par le controller dans $portfolio
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Freelancer</title>
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

<body class="bg-white min-h-screen text-gray-200">

    <!-- HEADER -->
<header class="w-full py-6 bg-transparent flex justify-between items-center px-10">
    
    <!-- TITRE CENTRÉ VISUELLEMENT (grâce au flex parent) -->
    <h1 class="text-4xl font-extrabold text-black tracking-wide mx-auto">
        PORTFOLIO <span class="text-green-400">WEBSITE</span>
    </h1>

    <!-- BOUTON À DROITE -->
    <a href="/project/index.php?route=logout"
       class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700 transition btn-3d">
       Déconnexion
    </a>

</header>


    <!-- CONTAINER -->
    <div class="max-w-5xl mx-auto mt-10 bg-white rounded-2xl shadow-2xl p-10 text-black">

        <!-- NAVBAR -->
        <nav class="flex justify-end mb-8 text-gray-600 font-semibold space-x-6">
            <a href="/project/view/frontoffice/freelancer/freelancer_home.php" class="hover:text-green-500">Home</a>
            <a href="/project/view/frontoffice/freelancer/about.php">About</a>
            <a href="/project/view/frontoffice/freelancer/skills.php">Skills</a>
            <a href="/project/view/frontoffice/freelancer/work.php">Work</a>
            <a href="/project/view/frontoffice/freelancer/contact.php" class="hover:text-green-500">Contact</a>
        </nav>

        

        <!-- ABOUT SECTION -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            <!-- LEFT: PHOTO -->
<div class="flex flex-col items-center">
    <img src="/project/uploads/<?= htmlspecialchars($portfolio['photo']) ?>"
         class="w-48 h-48 rounded-xl object-cover shadow-lg mb-2">

    <!-- Nom sous la photo -->
    <h3 class="text-xl font-semibold mb-2"> I'am <?= htmlspecialchars($_SESSION['name']) ?> </h3>

    
</div>


            <!-- RIGHT: TEXT -->
            <div>
                

                

                <p class="mt-4">
                    <span class="font-semibold">Tarif :</span>
                    <?= htmlspecialchars($portfolio['tarif']) ?> DT
                </p>

                <a href="/project/view/frontoffice/freelancer/edit_portfolio.php"
                   class="inline-block mt-6 bg-green-600 text-white px-6 py-2 rounded-lg shadow hover:bg-green-700 transition">
                    Modifier Profil
                </a>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="text-center mt-10 text-gray-400 text-sm">
        © 2025 Esprit — By Web Creator
    </footer>

</body>
</html>
