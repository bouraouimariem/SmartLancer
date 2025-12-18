<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 shadow-lg rounded-xl w-full max-w-md">

        <h2 class="text-2xl font-bold text-green-700 text-center mb-6">Nouveau mot de passe</h2>

        <form action="/project/index.php?route=update_password" method="POST">

    <label class="block mb-2 font-medium">Token reçu par email :</label>
    <input type="text" name="token" 
           class="w-full px-3 py-2 border rounded-lg mb-4">

    <label class="block mb-2 font-medium">Nouveau mot de passe :</label>
    <input type="password" name="password" 
           class="w-full px-3 py-2 border rounded-lg">

    <button class="w-full bg-green-700 text-white py-2 rounded-lg mt-5">
        Mettre à jour
    </button>
</form>


    </div>

</body>
</html>
