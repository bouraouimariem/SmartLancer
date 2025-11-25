<?php
session_start();
require_once '../../config.php'; // adapte le chemin si besoin


// Vérifier que l'utilisateur est connecté et que c'est un freelancer
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'freelance') {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$db = config::getConnexion();

// Vérifier si un profil existe déjà
$stmt = $db->prepare("SELECT * FROM profile WHERE id_utilisateur = :id");
$stmt->execute(['id' => $id_user]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Si le formulaire est soumis
if (isset($_POST['create_profile'])) {
    $photo_name = "";

    // ==== GESTION DE L’UPLOAD D’IMAGE ====
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "../../uploads/profiles/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_name = uniqid() . "." . strtolower($ext);
        $targetFile = $targetDir . $photo_name;

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array(strtolower($ext), $allowed)) {
            move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
        } else {
            echo "<script>alert('Format de photo invalide. Formats acceptés: JPG, PNG, WEBP');</script>";
            $photo_name = "";
        }
    }

    // ==== INSERTION DANS LA BASE ====
    $insert = $db->prepare("INSERT INTO profile (id_utilisateur, lien_p, photo, bio, experience, rate, competance)
                            VALUES (:id, :lien_p, :photo, :bio, :experience, :rate, :competance)");
    $insert->execute([
        'id' => $id_user,
        'lien_p' => $_POST['lien_p'],
        'photo' => $photo_name,
        'bio' => $_POST['bio'],
        'experience' => $_POST['experience'],
        'rate' => $_POST['rate'],
        'competance' => $_POST['competance']
    ]);

    echo "<script>alert('Profil créé avec succès !'); window.location.href='profile.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
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
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-15px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h2 {
            color: #6c63ff;
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            color: #333;
            margin-top: 12px;
            display: block;
        }

        input, textarea {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border-radius: 12px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: 0.3s;
        }

        input:focus, textarea:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 8px rgba(108,99,255,0.3);
            outline: none;
        }

        button {
            background: #6c63ff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            margin-top: 20px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s;
        }

        button:hover {
            background: #5848e2;
        }

        .profile-info {
            background: #f9f9f9;
            border-radius: 15px;
            padding: 20px;
            margin-top: 10px;
        }

        .profile-info p {
            margin: 10px 0;
            font-size: 15px;
            color: #333;
        }

        .avatar {
            display: block;
            margin: 0 auto 15px;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #6c63ff;
        }

        .back {
            text-align: center;
            margin-top: 20px;
        }

        .emoji {
            font-size: 24px;
        }
        .btn {
    display: inline-block;
    padding: 10px 20px;
    background: #4a4aff;
    color: white;
    border-radius: 6px;
    text-decoration: none;
}

    </style>
</head>
<body>

<div class="container">
    <h2>Bienvenue <?php echo htmlspecialchars($_SESSION['nom']); ?> 👋</h2>

    <?php if (!$profile): ?>
        <!-- ===== FORMULAIRE DE CRÉATION DE PROFIL ===== -->
        <form method="POST" enctype="multipart/form-data">
            <label>Photo de profil :</label>
            <input type="file" name="photo" accept="image/*">

            <label>Lien Portfolio :</label>
            <input type="text" name="lien_p" placeholder="Ex: https://monportfolio.com" required>

            <label>Bio :</label>
            <textarea name="bio" rows="3" placeholder="Parlez un peu de vous..." required></textarea>

            <label>Expérience :</label>
            <input type="text" name="experience" placeholder="Ex: 3 ans en développement web" required>

            <label>Compétences :</label>
            <input type="text" name="competance" placeholder="Ex: HTML, CSS, PHP, JavaScript" required>

            <label>Tarif (DT/h) :</label>
            <input type="number" step="0.01" name="rate" placeholder="Ex: 25.00" required>

            <button type="submit" name="create_profile">Créer mon profil</button>
        </form>

    <?php else: ?>
        <!-- ===== AFFICHAGE DU PROFIL EXISTANT ===== -->
        <?php if (!empty($profile['photo'])): ?>
            <img class="avatar" src="../../uploads/profiles/<?php echo htmlspecialchars($profile['photo']); ?>" alt="Photo de profil">
        <?php else: ?>
            <img class="avatar" src="../../uploads/profiles/default.png" alt="Photo de profil">
        <?php endif; ?>

        <div class="profile-info">
            <p><strong>💼 Lien Portfolio :</strong> <a href="<?php echo htmlspecialchars($profile['lien_p']); ?>" target="_blank"><?php echo htmlspecialchars($profile['lien_p']); ?></a></p>
            <p><strong>🧑‍🎓 Bio :</strong> <?php echo htmlspecialchars($profile['bio']); ?></p>
            <p><strong>💪 Expérience :</strong> <?php echo htmlspecialchars($profile['experience']); ?></p>
            <p><strong>⚙️ Compétences :</strong> <?php echo htmlspecialchars($profile['competance']); ?></p>
            <p><strong>💰 Tarif :</strong> <?php echo htmlspecialchars($profile['rate']); ?> DT/h</p>
        </div>

        <div class="back">
    <a href="pages/tete%20et%20pied/update_profile.php" class="btn">Modifier mon profil</a>
</div>

    <?php endif; ?>
</div>

</body>
</html>
