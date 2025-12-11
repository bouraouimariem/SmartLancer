<?php
require_once '../../../controller/publicationC.php';

$pubC = new publicationController();

if (!isset($_GET['id_pub'])) {
    die("ID publication manquant.");
}

$id_pub = $_GET['id_pub'];
$pub = $pubC->getPublicationById($id_pub);

if (!$pub) {
    die("Publication introuvable.");
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Publication</title>
    <link rel="stylesheet" href="../clientcss.css"> <!-- même CSS -->
</head>

<body>

<!-- ============ HEADER ============ -->
<header>
    <div class="logo-container">
        <span>SmartLancer</span>
    </div>
    <div class="page-title">✏️ Modifier votre publication</div>
</header>

<div class="container grid">

    <!-- ======= FORMULAIRE DE MODIFICATION ======= -->
    <div class="side-card" style="width:100%;">

        <h3 style="text-align:center;">Modifier : <?= htmlspecialchars($pub['nom_pub']) ?></h3>

        <form method="POST" action="update_publication.php">
            
            <input type="hidden" name="id_pub" value="<?= $pub['id_pub'] ?>">

            <label>Nom du projet :</label>
            <input type="text" name="nom_pub_modif" value="<?= htmlspecialchars($pub['nom_pub']) ?>">

            <label>Description :</label>
            <textarea name="description_modif" rows="3"><?= htmlspecialchars($pub['description']) ?></textarea>

            <label>Budget (DT) :</label>
            <input type="number" name="budget_modif" value="<?= $pub['budget'] ?>">

            <label>Délai (jours) :</label>
            <input type="number" name="delai_modif" value="<?= $pub['delai_requise'] ?>">

            <label>Catégorie :</label>
            <select name="categorie">
                <option <?= ($pub['categorie']=="web")?"selected":"" ?> value="web">Web</option>
                <option <?= ($pub['categorie']=="mobile")?"selected":"" ?> value="mobile">Mobile</option>
                <option <?= ($pub['categorie']=="design")?"selected":"" ?> value="design">Design</option>
                <option <?= ($pub['categorie']=="marketing")?"selected":"" ?> value="marketing">Marketing</option>
                <option <?= ($pub['categorie']=="ai")?"selected":"" ?> value="ai">IA</option>
            </select>

<button class="btn btn-info" style="width:100%; margin-top:20px;" 
        type="submit" name="submit_modif">
    Sauvegarder
</button>

        </form>

        <a href="../clientpub.php" class="btn btn-danger" style="margin-top:10px; display:block; text-align:center;">
            Annuler
        </a>

    </div>
</div>

</body>
</html>
