<?php
session_start();

// sécurité
if (!isset($_SESSION['email']) || $_SESSION['role'] !== "Freelancer") {
    header("Location: ../../index.php?route=login");
    exit();
}

// Si formulaire soumis
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    if (empty($name) || empty($email) || empty($message)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        
        // ⚠️ Mets ton adresse ici
        $receiving_email = "smartlancer@gmail.com";

        $subject = "Message de contact - Portfolio Freelancer";
        $body    = "Nom : $name\nEmail : $email\n\nMessage :\n$message";

        $headers = "From: $email";

        if (mail($receiving_email, $subject, $body, $headers)) {
            $success = "Votre message a été envoyé avec succès !";
        } else {
            $error = "Erreur lors de l’envoi du message.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Contact</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-lg">

    <h1 class="text-2xl font-bold text-center text-green-700 mb-6">
        Contactez-moi
    </h1>

    <?php if ($success): ?>
        <p class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">

        <div>
            <label class="block text-sm">Nom :</label>
            <input type="text" name="name" class="w-full p-2 border rounded" required>
        </div>

        <div>
            <label class="block text-sm">Email :</label>
            <input type="email" name="email" class="w-full p-2 border rounded" required>
        </div>

        <div>
            <label class="block text-sm">Message :</label>
            <textarea name="message" rows="4" class="w-full p-2 border rounded" required></textarea>
        </div>

        <button class="w-full bg-green-700 hover:bg-green-800 text-white p-2 rounded">
            Envoyer
        </button>

        <p class="text-center mt-3">
            <a href="/project/index.php?route=profil" class="text-green-700 underline">
                Retour au profil
            </a>
        </p>
    </form>

</div>

</body>
</html>
