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
  <title>Modifier mon portfolio</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200 min-h-screen flex justify-center items-center">

<div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-2xl">

  <h1 class="text-2xl font-bold text-center text-green-700 mb-6">
    Modifier mon portfolio
  </h1>

  <form action="/project/index.php?route=update_portfolio" method="POST" enctype="multipart/form-data">
 
      

    <div>
      <label class="block">Photo actuelle :</label>
      <img src="/project/uploads/<?= $portfolio['photo'] ?>" class="w-24 h-24 rounded-full mb-3">
      <input type="file" name="photo">
    </div>

    <div>
      <label>Lien :</label>
      <input type="url" name="lien" value="<?= $portfolio['lien'] ?>"
             class="w-full p-2 border rounded">
    </div>

    <div>
      <label>Bio :</label>
      <textarea name="bio" class="w-full p-2 border rounded"><?= $portfolio['bio'] ?></textarea>
    </div>

    <div>
      <label>Expérience :</label>
      <textarea name="experience" class="w-full p-2 border rounded"><?= $portfolio['experience'] ?></textarea>
    </div>

    <div>
      <label>Compétences :</label>
      <input type="text" id="competence" name="competence"
       class="w-full px-3 py-2 rounded-lg border border-gray-300"
       placeholder="Tapez une compétence...">

<div id="suggestions"
     class="bg-white border border-gray-300 rounded mt-1 hidden max-h-40 overflow-y-auto"></div>

<div id="tags" class="flex flex-wrap gap-2 mt-3"></div>

<input type="hidden" name="competence_tags" id="competence_tags">

    </div>

    <div>
      <label>Tarif (DT) :</label>
      <input type="number" name="tarif" value="<?= $portfolio['tarif'] ?>"
             class="w-full p-2 border rounded">
    </div>

    <button class="w-full bg-green-700 text-white p-2 rounded shadow btn-3d">
      Mettre à jour
    </button>

  </form>
</div>
<script src="portfolio.js"></script>

</body>
</html>
