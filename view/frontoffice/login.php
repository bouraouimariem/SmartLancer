<?php
$errors = $errors ?? [];
$old = $_POST ?? [];
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Se connecter</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
      /* Animation fade-in */
      .fade-in {
          opacity: 0;
          transform: translateY(20px);
          animation: fade 0.8s ease forwards;
      }
      @keyframes fade {
          to {
              opacity: 1;
              transform: translateY(0);
          }
      }

      /* Animation slide-in from left */
      .slide-left {
          opacity: 0;
          transform: translateX(-40px);
          animation: slideLeft 0.9s ease forwards;
      }
      @keyframes slideLeft {
          to {
              opacity: 1;
              transform: translateX(0);
          }
      }

      /* Animation slide-in from right */
      .slide-right {
          opacity: 0;
          transform: translateX(40px);
          animation: slideRight 0.9s ease forwards;
      }
      @keyframes slideRight {
          to {
              opacity: 1;
              transform: translateX(0);
          }
      }

      /* 3D hover animation */
      .btn-3d {
          transition: 0.25s ease;
      }
      .btn-3d:hover {
          transform: translateY(-4px) scale(1.03);
          box-shadow: 0 10px 18px rgba(0,0,0,0.20);
      }
  </style>
</head>

<body class="bg-gray-200 flex items-center justify-center min-h-screen">

  <!-- HEADER -->
  <header class="flex justify-between items-center bg-green-700 text-white shadow px-6 py-4 fixed w-full top-0 left-0 z-50 fade-in">
      <div class="flex items-center gap-3">
          <img src="/project/uploads/logo.png" alt="Logo SmartLancer" class="h-9 w-9 object-contain">
          <span class="text-xl font-semibold">SmartLancer</span>
      </div>
  </header>

  <!-- CONTAINER -->
  <div class="flex bg-white shadow-lg rounded-xl overflow-hidden w-full max-w-4xl mt-16 fade-in">

      <!-- COLONNE GAUCHE + ANIMATION SLIDE -->
      <div class="w-1/2 bg-green-700 text-white p-10 flex flex-col justify-center slide-left">
          <h1 class="text-4xl font-bold mb-4">Welcome Back!</h1>

          <p class="text-lg opacity-90 leading-relaxed">
              We are happy to have you with us again.
              <br> If you need anything, we are here to help.
          </p>
      </div>

      <!-- COLONNE DROITE + ANIMATION SLIDE -->
      <div class="w-1/2 p-10 slide-right">

          <h1 class="text-2xl font-bold text-green-700 text-center mb-6 ">Se connecter</h1>

          <!-- SUCCESS -->
          <?php if ($success): ?>
              <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded mb-4 fade-in">
                  <?= $success ?>
              </div>
          <?php endif; ?>

          <!-- SESSION ERROR -->
          <?php if ($error): ?>
              <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded mb-4 fade-in">
                  <?= $error ?>
              </div>
          <?php endif; ?>

          <!-- ERREURS -->
          <?php if (!empty($errors)): ?>
              <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded mb-4 fade-in">
                  <ul>
                      <?php foreach ($errors as $err): ?>
                          <li><?= $err ?></li>
                      <?php endforeach; ?>
                  </ul>
              </div>
          <?php endif; ?>

          <!-- FORMULAIRE -->
          <form action="index.php?route=login" method="POST" class="space-y-4">

              <div class="fade-in">
                  <label class="block text-sm">Email</label>
                  <input type="email" name="email"
                         value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                         class="w-full p-2 border rounded">
              </div>

              <div class="fade-in">
                  <label class="block text-sm">Mot de passe</label>
                  <input type="password" name="password"
                         class="w-full p-2 border rounded">
              </div>

              <button type="submit"
                      class="w-full bg-green-700 text-white p-2 rounded shadow btn-3d fade-in shadow btn-3d">
                  Se connecter
              </button>

              <p class="text-center text-sm mt-2 fade-in">
                  Pas encore de compte ?
                  <a href="index.php?route=register" class="text-green-700 shadow btn-3d">S'inscrire</a>
              </p>

              <p class="text-center mt-2 fade-in">
                  <a href="index.php?route=forgot_password" class="text-green-700 hover:underline shadow btn-3d">
                      Mot de passe oublié ?
                  </a>
              </p>

              

          </form>

      </div>
  </div>

  <!-- FOOTER -->
  <footer class="flex justify-center items-center bg-white text-black shadow px-6 py-4 w-full fixed bottom-0 left-0 z-50 fade-in">
      <div class="text-center">
          <span class="text-sm opacity-90">© 2025 Esprit — By Web Creator</span>
      </div>
  </footer>

</body>
</html>