<?php
$errors = $errors ?? []; //si il ya un erreur donc on le garde snn on creer un tableau vide
$old = $_POST ?? [];//recuperer ancien valeur
$success = $_SESSION['success'] ?? null;//inscription reusi
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Se connecter</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen">
  
  <header class="flex justify-between items-center bg-green-700 text-white shadow px-6 py-4 fixed w-full top-0 left-0 z-50">
    <div class="flex items-center gap-3">
     
      <img src="/project/uploads/logo.png" alt="Logo SmartLancer" class="h-9 w-9 object-contain">

     
      <span class="text-xl font-semibold">SmartLancer</span>
    </div>
    <div>
      
    </div>
  </header>
  <!--deux colone-->
  <div class="flex bg-white shadow-lg rounded-xl overflow-hidden w-full max-w-4xl mt-10"> 

    <!-- colonne gauche  -->
    <div class="w-1/2 bg-green-700 text-white p-10 flex flex-col justify-center">
      <h1 class="text-4xl font-bold mb-4">Welcome Back!</h1>

      <p class="text-lg opacity-90 leading-relaxed">
        We are happy to have you with us again.  
        If you need anything,  
        <br>we are here to help.
      </p>
    </div>

    <!-- colonne droite -->
    <div class="w-1/2 p-10"> <!--zone de formulaire-->

      <h1 class="text-2xl font-bold text-green-700 text-center mb-6">Se connecter</h1>

      <?php if ($success): ?>
        <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded mb-4">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded mb-4">
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

    <form action="index.php?route=login" method="POST" class="space-y-4"><!--envois les donne-->
      <div>
        <label class="block text-sm">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>"  class="w-full p-2 border rounded">
      </div>

      <div>
        <label class="block text-sm">Mot de passe</label>
        <input type="password" name="password"  class="w-full p-2 border rounded">
      </div>

      <button type="submit" class="w-full bg-green-700 text-white p-2 rounded">connecter</button>
      <p class="text-center text-sm mt-2">Pas encore de compte ? <a href="index.php?route=register" class="text-green-700">S'inscrire</a></p>

      <p class="text-center mt-2">
        <a href="view/frontoffice/forgot_password.php" class="text-green-700 hover:underline">
          Mot de passe oublié ?
        </a>
      </p>

    </form>
  </div>
  <footer class="flex justify-center items-center bg-white-700 text-black shadow px-6 py-4 w-full fixed bottom-0 left-0 z-50">
    <div class="text-center">
        
        <br>
        <span class="text-sm opacity-90">© 2025 Esprit — By Web Creator</span>
    </div>
</footer>
</body>
</html>
