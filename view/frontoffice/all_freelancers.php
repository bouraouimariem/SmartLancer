<?php


// 🔐 Vérifier que c’est un client
if (!isset($_SESSION['email']) || $_SESSION['role'] !== "Client") {
    header("Location: ../../index.php?route=login");
    exit();
}

// $freelancers envoyé par le controller
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Freelancers</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white min-h-screen text-gray-200">

<!-- HEADER -->
<header class="w-full py-6 bg-transparent flex justify-between items-center px-10">
    
    <!-- TITRE CENTRÉ VISUELLEMENT (grâce au flex parent) -->
    <h1 class="text-4xl text-center font-bold text-green-700 mt-10">
    Tous les Freelancers
</h1>

    <!-- BOUTON À DROITE -->
    <a href="./index.php?route=client"
       class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700 transition btn-3d">
       ⬅ Retour
    </a>

</header>



<div class="max-w-6xl mx-auto mt-10 grid grid-cols-1 md:grid-cols-3 gap-8">

    <?php foreach ($freelancers as $f): ?>
        <div class="bg-white shadow-lg rounded-xl p-5 text-center">
            
            <img src="/project/uploads/<?= htmlspecialchars($f['photo']) ?>"
                 class="w-32 h-32 rounded-full mx-auto mb-3 object-cover">

            <h2 class="text-xl font-semibold">
                <?= htmlspecialchars($f['name']) ?>
            </h2>

            <p class="text-gray-600">
                <?= substr(htmlspecialchars($f['bio']), 0, 80) ?>...
            </p>

            <p class="text-green-700 font-bold mt-2">
                <?= htmlspecialchars($f['tarif']) ?> DT / heure
            </p>

            <a href="/project/index.php?route=profil&id=<?= $f['id_utilisateur'] ?>"
               class="block mt-4 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                Voir Profil
            </a>

        </div>
    <?php endforeach; ?>

</div>

</body>
</html>
