<?php
session_start();
$old = $_SESSION['old'] ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);
if (empty($_SESSION['email']) || ($_SESSION['role'] ?? '') !== 'Freelancer') {
    header("Location: project/index.php?route=profil");
    exit();
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon Portfolio</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#d1d5db] flex items-center justify-center min-h-screen">

  <div class="bg-[#f9fafb] text-black rounded-2xl shadow-lg w-full max-w-2xl p-8">

    <h1 class="text-2xl font-semibold text-center mb-6 text-[#166534]">
      Bienvenue <?= htmlspecialchars($_SESSION['name']) ?>
    </h1>

    <?php if (!empty($errors)): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form id="portfolioForm" action="/project/index.php?route=create_portfolio" method="POST" enctype="multipart/form-data">



      <div>
        <label for="photo" class="block text-sm font-medium text-black mb-1">Photo de profil (optionnel) :</label>
        <input type="file" id="photo" name="photo" accept="image/*" class="block">
      </div>

      <div>
        <label for="lien" class="block text-sm font-medium text-black mb-1">Lien Portfolio :</label>
        <input type="url" id="lien" name="lien" placeholder="https://..." value="<?= htmlspecialchars($old['lien'] ?? '') ?>"
          class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-600">
      </div>

      <div>
        <label for="bio" class="block text-sm font-medium text-black mb-1">Bio :</label>
        <textarea id="bio" name="bio" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-300"><?= htmlspecialchars($old['bio'] ?? '') ?></textarea>
      </div>

      <div>
        <label for="experience" class="block text-sm font-medium text-black mb-1">Expérience :</label>
        <textarea id="experience" name="experience" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-300"><?= htmlspecialchars($old['experience'] ?? '') ?></textarea>
      </div>

      <div>
        <label for="competence" class="block text-sm font-medium text-black mb-1">Compétences :</label>
        <input type="text" id="competence" name="competence" placeholder="Ex: HTML, CSS, JS" value="<?= htmlspecialchars($old['competence'] ?? '') ?>"
          class="w-full px-3 py-2 rounded-lg border border-gray-300">
      </div>

      <div>
        <label for="tarif" class="block text-sm font-medium text-black mb-1">Tarif (DT) :</label>
        <input type="number" id="tarif" name="tarif" min="0" step="0.01" value="<?= htmlspecialchars($old['tarif'] ?? '') ?>"
          class="w-full px-3 py-2 rounded-lg border border-gray-300">
      </div>

      <button type="submit"
        class="w-full bg-[#166534] hover:bg-green-700 text-white font-medium py-2 rounded-lg transition">
        Créer  mon profil
      </button>

      <p class="text-center text-sm mt-4">
        <a href="freelancer_home.php" class="text-[#166534]">Retour</a>
      </p>

    </form>
  </div>
<script src="portfolio.js"></script>

</body>
</html>
