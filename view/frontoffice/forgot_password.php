<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md">

        <h2 class="text-2xl font-bold text-center text-green-700 mb-6">
            Réinitialiser le mot de passe
        </h2>

        <?php if (!empty($_GET['error'])): ?>
          <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
            <?= htmlspecialchars($_GET['error']) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($_GET['success'])): ?>
          <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            <?= htmlspecialchars($_GET['success']) ?>
          </div>
        <?php endif; ?>

        <form id="resetForm" action="/project/index.php?route=send_reset" method="POST">

            <label class="block mb-2 font-medium">Email :</label>
            <input 
                type="email" 
                name="email"
                id="email"
                required
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-700"
                placeholder="Entrez votre email"
            >

            <button 
                class="w-full bg-green-700 text-white py-2 rounded-lg mt-5 hover:bg-green-800 shadow btn-3d">
                Envoyer le lien de réinitialisation
            </button>

            <p class="text-center text-sm mt-4">
                <a href="/project/index.php?route=login" class="text-green-700">Retour au login</a>
            </p>

        </form>

    </div>

<script>
document.getElementById('resetForm').addEventListener('submit', function(e){
    const email = document.getElementById("email").value;

    if (!email.includes("@") || !email.includes(".")) {
        e.preventDefault();
        alert("Veuillez entrer un email valide !");
    }
});
</script>

</body>
</html>
