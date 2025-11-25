<?php
// view/register.php
$errors = $errors ?? [];
$old = $_POST ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Créer un compte</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen">
  <!-- Header -->
  <header class="flex justify-between items-center bg-green-700 text-white shadow px-6 py-4 fixed w-full top-0 left-0 z-50">
    <div class="flex items-center gap-3">
      <!-- Logo -->
      <img src="/project/uploads/logo.png" alt="Logo SmartLancer" class="h-9 w-9 object-contain">

      <!-- Nom du site -->
      <span class="text-xl font-semibold">SmartLancer</span>
    </div>
    <div>
      
    </div>
  </header>



  <!-- CONTAINER (2 colonnes) -->
  <div class="flex bg-white shadow-lg rounded-xl overflow-hidden w-full max-w-4xl mt-20">

    <!-- LEFT : WELCOME -->
    <div class="w-1/2 bg-green-700 text-white p-10 flex flex-col justify-center">
      <h1 class="text-3xl font-bold mb-4">WELCOME!</h1>
      <p class="text-lg opacity-90">
        Nous sommes ravis de vous accueillir.  
        Créez votre compte pour rejoindre SmartLancer.
      </p>
    </div>

    <!-- RIGHT : FORMULAIRE -->
    <div class="w-1/2 p-10">
      <h1 class="text-2xl font-bold text-green-700 mb-6 text-center">Créer un compte</h1>

      <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded mb-4">
          <ul class="list-disc pl-5">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

    <form id="registerForm" action="index.php?route=register" method="POST" class="space-y-4">
      <div>
        <label class="block text-sm">Nom complet</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>"  class="w-full p-2 border rounded">
      </div>

      <div>
        <label class="block text-sm">Email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>"  class="w-full p-2 border rounded">
      </div>

      <div>
        <label class="block text-sm">Mot de passe</label>
        <input type="password" name="password" id="password"  class="w-full p-2 border rounded">
      </div>

      <div>
        <label class="block text-sm">Rôle</label>
        <select name="role" id="role"  class="w-full p-2 border rounded">
          <option value="">-- Choisir un rôle --</option>
          <option value="Client">Client</option>
          <option value="Freelancer">Freelancer</option>
          <option value="Admin">Admin</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <input type="checkbox" id="conditions" name="conditions">
        <label for="conditions" class="text-sm">J'accepte les conditions</label>
      </div>

      <button type="submit" class="w-full bg-green-700 text-white p-2 rounded">S'inscrire</button>
      <p class="text-center text-sm mt-2">Vous avez déjà un compte ? <a href="index.php?route=login" class="text-green-700">Se connecter</a></p>
    </form>
  </div>

  <script src="register.js"></script>
  <footer class="flex justify-center items-center bg-white-700 text-black shadow px-6 py-4 w-full fixed bottom-0 left-0 z-50">
    <div class="text-center">
        
        <br>
        <span class="text-sm opacity-90">© 2025 Esprit — By Web Creator</span>
    </div>
</footer>

</body>

</html>
