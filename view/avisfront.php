<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/avis.php';

$message = '';
$database = new Database();
$db = $database->getConnection();
$avisModel = new Avis($db);

$isEdit = false;
$avisData = [
    'nom' => '',
    'email' => '',
    'note' => '',
    'contenu' => ''
];

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];
    $avis = $avisModel->getAvisById($id);
    if ($avis) {
        $avisData = [
            'nom' => $avis['nom'],
            'email' => $avis['email'],
            'note' => $avis['note'],
            'contenu' => $avis['contenu']
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $note = isset($_POST['note']) ? (int)$_POST['note'] : 0;
    $contenu = htmlspecialchars($_POST['avis']);

    if (!empty($nom) && !empty($email) && $note >= 1 && $note <= 5 && !empty($contenu)) {
        if ($isEdit) {
            if ($avisModel->updateAvis($id, $nom, $email, $note, $contenu)) {
                header('Location: /validationmodule/view/profilfreelancer.php');
                exit;
            } else {
                $message = "Erreur : impossible de modifier l'avis.";
            }
        } else {
            if ($avisModel->addAvis($nom, $email, $note, $contenu)) {
                header('Location: /validationmodule/view/profilfreelancer.php');
                exit;
            } else {
                $message = "Erreur : impossible d'ajouter votre avis. Veuillez réessayer.";
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Avis & Évaluations - SmartLancer</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
    :root {
        --green: #075e3a;
        --green-dark: #075e3a;
        --green-light: #c7f6e4;
        --bg-body: #edf2f7;
        --card-bg: #ffffff;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: var(--bg-body);
        margin: 0;
        color: #333;
    }

    main {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 90vh;
        padding: 20px;
    }

    .avis-box {
        background: var(--card-bg);
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        padding: 40px;
        width: 100%;
        max-width: 500px;
        border: 3px solid var(--green-light);
        transition: 0.3s;
    }

    .avis-box:hover { transform: translateY(-3px); }

    .avis-box h2 {
        text-align: center;
        color: var(--green-dark);
        margin-bottom: 25px;
        font-size: 28px;
    }

    .form-avis {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .form-avis label {
        font-weight: 600;
        font-size: 14px;
        color: #333;
    }

    .form-avis input, 
    .form-avis textarea {
        width: 100%;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid #cdded7;
        font-size: 15px;
        font-family: inherit;
        transition: all 0.3s ease;
    }

    .form-avis input:focus, 
    .form-avis textarea:focus {
        outline: none;
        border-color: var(--green);
        box-shadow: 0 0 10px rgba(15,163,107,0.3);
    }

    textarea { resize: none; height: 120px; }

    .rating { display: flex; justify-content: flex-start; gap: 10px; }

    .rating input { display: none; }

    .rating label {
        font-size: 28px;
        color: #ccc;
        cursor: pointer;
        transition: color 0.25s ease;
    }

    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label { color: #FFC107; }

    .btn {
        background-color: var(--green);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px 20px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 10px;
    }

    .btn:hover {
        background-color: var(--green-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(15,163,107,0.35);
    }

    .message {
        text-align: center;
        font-weight: bold;
        color: var(--green-dark);
        margin-bottom: 15px;
    }

    @media (max-width: 600px) {
        .avis-box { padding: 30px; }
    }
</style>
</head>

<body>
<main>
    <div class="avis-box">
        <h2><?= $isEdit ? "Modifier l'avis" : "Laisser un avis" ?></h2>

        <?php if($message): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>

        <form action="" method="post" class="form-avis">
            <label for="nom">Nom complet</label>
            <input type="text" id="nom" name="nom" placeholder="Votre nom" value="<?= htmlspecialchars($avisData['nom']) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Votre email" value="<?= htmlspecialchars($avisData['email']) ?>" required>

            <label>Votre note</label>
            <div class="rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>" name="note" value="<?= $i ?>" <?= ($avisData['note'] == $i) ? 'checked' : '' ?>>
                    <label for="star<?= $i ?>">★</label>
                <?php endfor; ?>
            </div>

            <label for="avis">Votre avis</label>
            <textarea id="avis" name="avis" placeholder="Écrivez votre retour..." required><?= htmlspecialchars($avisData['contenu']) ?></textarea>

            <button type="submit" class="btn"><?= $isEdit ? "Enregistrer les modifications" : "Envoyer l'avis" ?></button>
        </form>
    </div>
</main>
</body>
</html>
