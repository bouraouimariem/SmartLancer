<?php
session_start();
require_once __DIR__ . "/../../Controller/ReclamationController.php";

$controller = new ReclamationController();
$successMsg = '';
$lastRec = null;
$isEdit = false;

// Mode édition (lorsque clic sur "Modifier" depuis le tableau)
if (isset($_GET['edit_id'])) {
    $lastRec = $controller->get((int)$_GET['edit_id']);
    if ($lastRec) $isEdit = true;
}

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $status = $_POST['statut'] ?? 'En attente';

    if ($action === 'add') {
        $controller->add($nom, $email, $sujet, $message, $status);
        $successMsg = "Merci ! Votre réclamation a été envoyée.";
        $lastRec = null;
        $isEdit = false;
    } elseif ($action === 'update' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $controller->update($id, $nom, $email, $sujet, $message, $status);
        $successMsg = "Votre réclamation a été mise à jour.";
        $lastRec = null; // vide le formulaire après modification
        $isEdit = false;
    }
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
        <p class="success-message"><?= htmlspecialchars($successMsg) ?></p>
    <?php endif; ?>

    <form id="rec-form" method="POST" action="index.php" novalidate>
        <input type="hidden" name="action" id="form-action" value="<?= $isEdit ? 'update' : 'add' ?>">
        <?php if($isEdit && $lastRec): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($lastRec['id']) ?>">
        <?php endif; ?>

        <label>Nom complet</label>
        <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($lastRec['nom'] ?? '') ?>" required>
        <span class="field-error" aria-live="polite"></span>

        <label> Adresse email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($lastRec['email'] ?? '') ?>" required>
        <span class="field-error" aria-live="polite"></span>

        <label>Sujet de la reclamation</label>
        <input type="text" name="sujet" id="sujet" value="<?= htmlspecialchars($lastRec['sujet'] ?? '') ?>" required>
        <span class="field-error" aria-live="polite"></span>

        <label>Message</label>
        <textarea name="message" id="message" rows="5" required><?= htmlspecialchars($lastRec['message'] ?? '') ?></textarea>
        <span class="field-error" aria-live="polite"></span>

        <input type="hidden" name="statut" value="<?= htmlspecialchars($lastRec['statut'] ?? 'En attente') ?>">

        <div class="actions">
            <button type="submit"><?= $isEdit ? 'Mettre à jour' : 'Envoyer la réclamation' ?></button>
            <a href="landing.php" class="button back">Annuler</a>
            <a href="reclamations.php" class="button add-new">Mes réclamations</a>
        </div>
    </form>
</div>

<footer>
    <p>© <?= date('Y') ?> Service de Réclamation — Tous droits réservés</p>
</footer>

<script src="reclamation.js"></script>
</body>
</html>
