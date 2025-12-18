<?php

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renvoyer l'email de vérification - SmartLancer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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

    <div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-md mt-16 fade-in">

        <div class="text-center mb-6">
            <div class="text-6xl mb-4">📧</div>
            <h2 class="text-3xl font-bold text-gray-800">Email de vérification</h2>
            <p class="text-gray-600 mt-2">Entrez votre email pour recevoir un nouveau lien de vérification</p>
        </div>

        <?php if ($error): ?>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 fade-in" role="alert">
            <strong>⚠️ Erreur :</strong> <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 fade-in" role="alert">
            <strong>✅ Succès :</strong> <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <form action="index.php?route=resend_verification_submit" method="POST">

            <div class="mb-5">
                <label class="block text-gray-700 font-semibold mb-2" for="email">
                    Adresse email
                </label>
                <input 
                    type="email" 
                    name="email"
                    id="email"
                    
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-700 transition"
                    placeholder="exemple@email.com"
                >
            </div>

            <button 
                type="submit"
                class="w-full bg-green-700 text-white font-bold py-3 rounded-lg hover:bg-green-800 transition shadow btn-3d">
                📨 Renvoyer l'email de vérification
            </button>

            <div class="text-center mt-6">
                <a href="index.php?route=login" class="text-green-700 hover:text-green-900 font-medium">
                    ← Retour à la connexion
                </a>
            </div>

        </form>

    </div>

    <!-- FOOTER -->
    <footer class="flex justify-center items-center bg-white text-black shadow px-6 py-4 w-full fixed bottom-0 left-0 z-50 fade-in">
        <div class="text-center">
            <span class="text-sm opacity-90">© 2025 Esprit — By Web Creator</span>
        </div>
    </footer>

</body>
</html>
