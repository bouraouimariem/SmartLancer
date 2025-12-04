<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== "Freelancer") {
    header("Location: ../../index.php?route=login");
    exit();
}

require_once __DIR__ . '/../../../model/Portfolio.php';
$model = new Portfolio();
$portfolio = $model->getByUserId($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Work</title>
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

<body class="bg-white min-h-screen p-20">
<!-- HEADER -->
<header class="flex justify-between items-center bg-green-700 text-white shadow px-6 py-4 fixed w-full top-0 left-0 z-50 fade-in">
    <div class="flex items-center gap-3">
        <img src="/project/uploads/logo.png" alt="logo" class="h-9 w-9 rounded-md">
        <span class="text-xl font-semibold">SmartLancer</span>
    </div>

    <a href="/project/index.php?route=profil"
       class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 shadow btn-3d">
       ⬅ Retour
    </a>
    
</header>



<div class="max-w-3xl bg-gray-100 p-8 rounded-xl shadow">

    <h1 class="text-3xl font-bold text-green-700 mb-6">My Work</h1>

    <p class="text-gray-700 leading-relaxed">
        <?= nl2br(htmlspecialchars($portfolio['experience'])) ?>
    </p>

</div>



<!-- FOOTER -->
<footer class="fixed bottom-0 left-0 w-full bg-white shadow-inner py-3 text-center text-gray-600 text-sm fade-in">
    © 2025 Esprit — By Web Creator
</footer>

</body>
</html>
