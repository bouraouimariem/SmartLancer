<?php
session_start();
require_once "../../Model/Reclamation.php";

$model = new Reclamation();
$successMsg = '';
$lastRec = null;
$isEdit = false;

// Réinitialiser la session si on vient de landing.php
if(!isset($_SESSION['lastRecId']) || (isset($_SERVER['HTTP_REFERER']) && basename($_SERVER['HTTP_REFERER']) === 'landing.php')){
    unset($_SESSION['lastRecId']);
}

// Déterminer si l'utilisateur veut modifier
if (isset($_GET['edit_id'])) {
    $lastRec = $model->getReclamation($_GET['edit_id']);
    if ($lastRec) $isEdit = true;
}

// Ajouter une réclamation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $model->addReclamation($_POST['nom'], $_POST['email'], $_POST['sujet'], $_POST['message']);
    $lastId = $model->getLastId();
    $_SESSION['lastRecId'] = $lastId;
    $successMsg = "Merci ! Votre réclamation a été envoyée.";
    $lastRec = $model->getReclamation($lastId);
    $isEdit = true; 
}

// Modifier une réclamation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $model->updateReclamation($_POST['id'], $_POST['nom'], $_POST['email'], $_POST['sujet'], $_POST['message']);
    $lastRec = $model->getReclamation($_POST['id']);
    $successMsg = "Votre réclamation a été mise à jour.";
    $isEdit = true;
}

// Supprimer une réclamation
if (isset($_GET['delete_id'])) {
    $model->deleteReclamation($_GET['delete_id']);
    if (isset($_SESSION['lastRecId']) && $_SESSION['lastRecId'] == $_GET['delete_id']) {
        unset($_SESSION['lastRecId']);
    }
    $successMsg = "Votre réclamation a été supprimée.";
    $lastRec = null;
    $isEdit = false;
}

// Charger la dernière réclamation si existe
if (!$lastRec && isset($_SESSION['lastRecId'])) {
    $lastRec = $model->getReclamation($_SESSION['lastRecId']);
    if($lastRec) $isEdit = true;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déposer une réclamation</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">
    <img src="logo.png" alt="Logo" class="logo">
    <h1><?= $isEdit ? 'Modifier votre réclamation' : 'Déposer votre réclamation' ?></h1>

    <?php if($successMsg): ?>
        <p class="success-message"><?= $successMsg ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'add' ?>">
        <?php if($isEdit && $lastRec): ?>
            <input type="hidden" name="id" value="<?= $lastRec['id'] ?>">
        <?php endif; ?>

        <input type="text" name="nom" placeholder="Nom complet" value="<?= $lastRec['nom'] ?? '' ?>" required>
        <span class="error"></span>
        <input type="email" name="email" placeholder="Email" value="<?= $lastRec['email'] ?? '' ?>" required>
        <span class="error"></span>
        <input type="text" name="sujet" placeholder="Sujet" value="<?= $lastRec['sujet'] ?? '' ?>" required>
        <span class="error"></span>

        <textarea name="message" placeholder="Votre message" rows="4" required><?= $lastRec['message'] ?? '' ?></textarea>
        <span class="error"></span>

        <div class="actions">
            <?php if($lastRec): ?>
                <a href="?delete_id=<?= $lastRec['id'] ?>" class="button delete" onclick="return confirm('Voulez-vous vraiment supprimer ?');">Supprimer</a>
                <button type="submit" class="button edit">Modifier</button>
            <?php endif; ?>

            <button type="submit" class="button add-new" <?= $isEdit ? 'formaction="?add"' : '' ?>>Envoyer la réclamation</button>
        </div>
    </form>

    <div class="back-zone">
        <a href="landing.php" class="button back">Retour à l'accueil</a>
    </div>
</div>

<footer>
    <p>© <?= date('Y') ?> Service de Réclamation — Tous droits réservés</p>
</footer>
<script src="reclamation.js"></script>

</body>
</html>
