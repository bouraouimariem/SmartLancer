<?php
include '../../controller/publicationC.php';

// ID user temporaire pour les tests (pas de connexion)
$id_user = 1;

$publicationC = new publicationController();

// Liste des publications du user
$list = $publicationC->listpub_for_user($id_user);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartLancer</title>
  <script src="clientpub.js"></script>

  <link rel="stylesheet" href="clientcss.css">
  <link rel="icon" type="image/png" href="img/logo.png">

</head>

<body>

<!-- ================= HEADER ================= -->
<header>
  <div class="logo-container">
    <span>SmartLancer</span>
  </div>
  <div class="page-title">Mes Publications</div>
</header>

<div class="container grid">

  <div>
    <h3>Vos Publications</h3>

    <?php foreach ($list as $pub): ?>
      <div class="card">
        <h4><?= htmlspecialchars($pub['nom_pub']) ?></h4>
        <p><?= htmlspecialchars($pub['description']) ?></p>

        <div class="details">
    <div>💰 Budget : <?= $pub['budget'] ?> DT</div>
    <div>⏱ Délai : <?= $pub['delai_requise'] ?> jours</div>
    <div>📅 Date : <?= $pub['date_pub'] ?></div>
    <div>📂 Catégorie : <?= htmlspecialchars($pub['categorie']) ?></div>
    <div>🔖 Statut : <?= $pub['status'] ?></div>
</div>


       <div class="actions">
    <a href="pages/modifier_publication.php?id_pub=<?= $pub['id_pub'] ?>" class="btn btn-warning">Modifier</a>
    <a href="pages/delete_publication.php?id_pub=<?= $pub['id_pub'] ?>" class="btn btn-danger">Supprimer</a>
</div>

      </div>
    <?php endforeach; ?>
  </div>

  <div class="side-card">
    <h4>Créer un Nouveau Projet</h4>
      <form method="POST" action="pages/ajouter_publication.php" onsubmit="return valider_publication()">
      <input id="nom_pub" type="text" name="nom_pub" placeholder="Nom du projet">
      <textarea id="description" name="description"  rows="3" placeholder="Décrivez votre projet..."></textarea>
      <input id="budget" type="number" name="budget"  placeholder="Budget (DT)">
      <input id="delai" type="number" name="delai" placeholder="Délai (jours)">
      <label>Catégories :</label>
      <label><input id="cat_web" type="checkbox" name="categories[]" value="web"> Web</label>
      <label><input id="cat_mobile" type="checkbox" name="categories[]" value="mobile"> Mobile</label>
      <label><input id="cat_design" type="checkbox" name="categories[]" value="design"> Design</label>
      <label><input id="cat_marketing" type="checkbox" name="categories[]" value="marketing"> Marketing</label>
      <label><input id="cat_ai" type="checkbox" name="categories[]" value="ai"> IA</label>
      <button class="btn btn-info" style="width:100%; margin-top:15px;">Publier</button>
    </form>
  </div>
<!-- ================= SCRIPTS ================= -->
<script>
  function togglePropo(id) {
    var div = document.getElementById("propo-" + id);
    div.style.display = (div.style.display === "none") ? "block" : "none";
  }

  function toggleEdit(id) {
    var div = document.getElementById("edit-" + id);
    div.style.display = (div.style.display === "none") ? "block" : "none";
  }
</script>

</body>
</html>
