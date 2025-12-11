<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/avis.php';
require_once __DIR__ . '/../model/validator.php';

$message = '';
$messageType = 'info';
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
    // Réinitialiser les erreurs
    Validator::resetErrors();

    // Récupérer et valider les données
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $note = isset($_POST['note']) ? (int)$_POST['note'] : 0;
    $contenu = $_POST['avis'] ?? '';

    // Validations côté serveur
    $isValid = true;
    if (!Validator::validateNom($nom)) {
        $isValid = false;
    }
    if (!Validator::validateEmail($email)) {
        $isValid = false;
    }
    if (!Validator::validateNote($note)) {
        $isValid = false;
    }
    if (!Validator::validateContenu($contenu)) {
        $isValid = false;
    }

    if (!$isValid) {
        $message = 'Veuillez corriger les erreurs suivantes: ' . implode(', ', array_values(Validator::getErrors()));
        $messageType = 'error';
    } else {
        // Nettoyer les données
        $nom = Validator::sanitize($nom);
        $email = Validator::sanitize($email);
        $contenu = Validator::sanitize($contenu);

        if ($isEdit) {
            if ($avisModel->updateAvis($id, $nom, $email, $note, $contenu)) {
                $message = 'Avis modifié avec succès!';
                $messageType = 'success';
                header('Refresh: 2; url=/validationmodule/view/profilfreelancer.php');
            } else {
                $message = "Erreur : impossible de modifier l'avis.";
                $messageType = 'error';
            }
        } else {
            if ($avisModel->addAvis($nom, $email, $note, $contenu)) {
                $message = 'Avis ajouté avec succès!';
                $messageType = 'success';
                header('Refresh: 2; url=/validationmodule/view/profilfreelancer.php');
            } else {
                $message = "Erreur : impossible d'ajouter votre avis. Veuillez réessayer.";
                $messageType = 'error';
            }
        }
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
/* Global styles */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html, body {
    min-height: 100%;
    width: 100%;
    font-family: "Poppins", sans-serif;
    background: linear-gradient(135deg, #d0e8ff, #f0f9ff);
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    padding: 20px 0;
    color: #333;
}

main {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 90vh;
    padding: 20px;
    width: 100%;
}

/* Container */
.avis-box {
    width: 95%;
    max-width: 700px;
    background: rgba(255, 255, 255, 0.95);
    color: #333;
    padding: 25px 25px;
    border-radius: 15px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    animation: fadeIn 0.8s ease-in-out;
    border: 1px solid #e0f0ff;
    transition: 0.3s;
}

.avis-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(30,144,255,0.2);
}

/* Title */
.avis-box h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 28px;
    color: #1E90FF;
    letter-spacing: 1px;
}

/* Form */
.form-avis {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

label {
    font-weight: bold;
    display: block;
    margin-top: 15px;
    color: #333;
    font-size: 14px;
}

input, textarea, select {
    width: 100%;
    padding: 12px;
    margin-top: 6px;
    border: 1px solid #ccc;
    border-radius: 12px;
    font-size: 16px;
    background: #f9f9f9;
    color: #333;
    transition: all 0.3s ease;
}

textarea {
    resize: none;
    height: 120px;
}

input:focus, textarea:focus, select:focus {
    outline: 2px solid #1E90FF;
    background: #fff;
    border-color: #1E90FF;
    box-shadow: 0 0 8px rgba(30,144,255,0.4);
}

/* Rating */
.rating {
    display: flex;
    justify-content: flex-start;
    gap: 10px;
    margin-top: 6px;
}

.rating input {
    display: none;
}

.rating label {
    font-size: 28px;
    color: #ccc;
    cursor: pointer;
    transition: color 0.25s ease;
    margin-top: 0;
}

.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label {
    color: #FFC107;
}

/* Button */
button, .btn {
    background: #1E90FF;
    padding: 12px 20px;
    color: #fff;
    border: none;
    border-radius: 15px;
    font-size: 16px;
    cursor: pointer;
    text-align: center;
    transition: 0.3s;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    margin-top: 10px;
}

button:hover, .btn:hover {
    background: #0a74d6;
    transform: scale(1.03);
    box-shadow: 0 6px 20px rgba(30,144,255,0.3);
}

/* Message */
.message {
    text-align: center;
    font-weight: bold;
    color: #1E90FF;
    margin-bottom: 15px;
    padding: 12px;
    background: #f0f8ff;
    border-radius: 8px;
}

/* Error message */
.error-message {
    color: red;
    font-size: 13px;
    margin-top: 2px;
    display: block;
    min-height: 18px;
}

input.error, textarea.error {
    border: 2px solid red !important;
    animation: shake 0.3s;
}

input.success, textarea.success {
    border: 2px solid green !important;
}

@keyframes shake {
    0% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    50% { transform: translateX(5px); }
    75% { transform: translateX(-5px); }
    100% { transform: translateX(0); }
}

@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(-10px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 600px) {
    .avis-box {
        padding: 25px 15px;
        width: 95%;
    }

    button, .btn {
        font-size: 15px;
        padding: 10px 16px;
    }

    textarea {
        height: 100px;
    }
}
</style>
</head>

<body>
<main>
    <div class="avis-box">
        <h2><?= $isEdit ? "Modifier l'avis" : "Laisser un avis" ?></h2>

        <?php if($message): ?>
            <p class="message" style="background-color: <?= $messageType === 'error' ? '#ffebee' : ($messageType === 'success' ? '#e8f5e9' : '#f0f8ff') ?>; color: <?= $messageType === 'error' ? '#c62828' : ($messageType === 'success' ? '#2e7d32' : '#0277bd') ?>; padding: 12px; border-radius: 8px; margin-bottom: 15px;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form action="" method="post" class="form-avis">
            <label for="nom">Nom complet <span style="color: #dc3545;">*</span></label>
            <input type="text" id="nom" name="nom" placeholder="Votre nom" value="<?= htmlspecialchars($avisData['nom']) ?>">
            <span id="nom_error" class="error-message"></span>

            <label for="email">Email <span style="color: #dc3545;">*</span></label>
            <input type="text" id="email" name="email" placeholder="Votre email" value="<?= htmlspecialchars($avisData['email']) ?>">
            <span id="email_error" class="error-message"></span>

            <label>Votre note <span style="color: #dc3545;">*</span></label>
            <div class="rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>" name="note" value="<?= $i ?>" <?= ($avisData['note'] == $i) ? 'checked' : '' ?>>
                    <label for="star<?= $i ?>">★</label>
                <?php endfor; ?>
            </div>
            <span id="note_error" class="error-message"></span>

            <label for="avis">Votre avis <span style="color: #dc3545;">*</span></label>
            <textarea id="avis" name="avis" placeholder="Écrivez votre avis..." ><?= htmlspecialchars($avisData['contenu']) ?></textarea>
            <span id="avis_error" class="error-message"></span>

            <button type="submit" class="btn"><?= $isEdit ? "Enregistrer les modifications" : "Envoyer l'avis" ?></button>
        </form>
    </div>
</main>

<script src="avisfront.js"></script>
</body>
</html>
