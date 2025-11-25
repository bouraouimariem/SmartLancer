<?php
session_start();
require_once '../../../../config.php';
// Vérifier que le freelance est connecté
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'freelance') {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$db = config::getConnexion();

// Récupérer le profil existant
$stmt = $db->prepare("SELECT * FROM profile WHERE id_utilisateur = :id");
$stmt->execute(['id' => $id_user]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    header("Location: profile.php"); // impossible de modifier si pas encore créé
    exit();
}

// === TRAITEMENT DU FORMULAIRE ===
if (isset($_POST['update_profile'])) {

    $photo_name = $profile['photo']; // par défaut garder l’ancienne

    // Gestion nouvelle photo
    if (!empty($_FILES['photo']['name'])) {

$targetDir = "../../../../uploads/profiles/";

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {

            // Supprimer l’ancienne photo sauf default.png
            if (!empty($profile['photo']) && $profile['photo'] != "default.png") {
                $oldPath = $targetDir . $profile['photo'];
                if (file_exists($oldPath)) unlink($oldPath);
            }

            // Upload nouvelle photo
            $photo_name = uniqid() . "." . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], $targetDir . $photo_name);

        } else {
            echo "<script>alert('Format de photo invalide. JPG, PNG, WEBP uniquement');</script>";
        }
    }

    // Mise à jour dans la BDD
    $update = $db->prepare("
        UPDATE profile SET 
            lien_p = :lien_p,
            photo = :photo,
            bio = :bio,
            experience = :experience,
            rate = :rate,
            competance = :competance
        WHERE id_utilisateur = :id
    ");

    $update->execute([
        'lien_p' => $_POST['lien_p'],
        'photo' => $photo_name,
        'bio' => $_POST['bio'],
        'experience' => $_POST['experience'],
        'rate' => $_POST['rate'],
        'competance' => $_POST['competance'],
        'id' => $id_user
    ]);

echo "<script>alert('Profil mis à jour avec succès !'); window.location.href='../../profile.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6c63ff, #836fff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 500px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        h2 {
            color: #6c63ff;
            text-align: center;
            margin-bottom: 25px;
        }

        label { font-weight: 600; display: block; margin-top: 12px; }
        input, textarea {
            width: 100%; padding: 10px; border-radius: 12px;
            border: 1px solid #ccc; margin-top: 6px;
        }

        button {
            background: #6c63ff; color: white; border: none;
            padding: 12px; width: 100%; border-radius: 12px;
            margin-top: 20px;
        }

        .avatar {
            width: 120px; height: 120px; border-radius: 50%;
            object-fit: cover; display: block; margin: 0 auto 15px;
            border: 3px solid #6c63ff;
        }
    </style>
</head>

<body>
<div class="container">

    <h2>Modifier mon profil ✏️</h2>

<img class="avatar" src="../../../../uploads/profiles/<?php echo htmlspecialchars($profile['photo']); ?>" alt="Photo de profil">

    <form method="POST" enctype="multipart/form-data">

        <label>Nouvelle photo :</label>
        <input type="file" name="photo" accept="image/*">

        <label>Lien Portfolio :</label>
        <input type="text" name="lien_p" value="<?php echo htmlspecialchars($profile['lien_p']); ?>" required>

        <label>Bio :</label>
        <textarea name="bio" rows="3" required><?php echo htmlspecialchars($profile['bio']); ?></textarea>

        <label>Expérience :</label>
        <input type="text" name="experience" value="<?php echo htmlspecialchars($profile['experience']); ?>" required>

        <label>Compétences :</label>
        <input type="text" name="competance" value="<?php echo htmlspecialchars($profile['competance']); ?>" required>

        <label>Tarif (DT/h) :</label>
        <input type="number" step="0.01" name="rate" value="<?php echo htmlspecialchars($profile['rate']); ?>" required>

        <button type="submit" name="update_profile">Enregistrer les modifications</button>
    </form>

</div>
</body>
</html>
