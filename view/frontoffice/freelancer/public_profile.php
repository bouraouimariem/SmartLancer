<?php
// $portfolio déjà fourni
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Freelancer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen text-gray-800">

<a href="./index.php?route=all_freelancers"
       class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700 transition btn-3d">
       ⬅ Retour
    </a>

<div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-10 mt-10">

    <div class="flex flex-col items-center">
        <img src="/project/uploads/<?= htmlspecialchars($portfolio['photo']) ?>"
             class="w-40 h-40 rounded-full object-cover shadow mb-4">

        <h2 class="text-3xl font-bold text-green-700">
            <?= htmlspecialchars($portfolio['name'] ?? 'Freelancer') ?>
        </h2>

        <p class="text-gray-600 mt-2"><?= nl2br(htmlspecialchars($portfolio['bio'])) ?></p>
    </div>

    <div class="mt-8 space-y-4">

        <div>
            <h3 class="font-bold text-green-600 text-xl">Expérience</h3>
            <p><?= nl2br(htmlspecialchars($portfolio['experience'])) ?></p>
        </div>

        <div>
            <h3 class="font-bold text-green-600 text-xl">Compétences</h3>
            <p><?= htmlspecialchars($portfolio['competence']) ?></p>
        </div>

        <div>
            <h3 class="font-bold text-green-600 text-xl">Tarif</h3>
            <p class="font-semibold"><?= htmlspecialchars($portfolio['tarif']) ?> DT / heure</p>
        </div>

    </div>

</div>

</body>
</html>
