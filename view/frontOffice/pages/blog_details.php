<?php
include_once '../../../controller/blogC.php';
include_once '../../../controller/commentaireC.php';
include_once '../../../model/commentaire.php';
include 'tete et pied/tetec.php';

$blogC = new BlogController();
$commentaireC = new CommentaireC();

// Récupération du blog
if (!isset($_GET['id_blog'])) {
    header('Location: blogs.php');
    exit;
}

$id_blog = $_GET['id_blog'];
$blog = $blogC->getBlogById($id_blog);

if (!$blog) {
    header('Location: blogs.php');
    exit;
}

// ✅ Incrémenter le compteur de vues
try {
    $db = config::getConnexion();
    $stmt = $db->prepare("UPDATE blog SET nb_view = nb_view + 1 WHERE id_blog = :id");
    $stmt->execute(['id' => $id_blog]);
} catch (Exception $e) {
    // On ignore
}

// ✅ Ajouter un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namec = $_POST['namec'];
    $contenue = $_POST['contenue'];
    $id_blog = $_POST['id_blog'];
    $date_com = date('Y-m-d H:i:s');

    $commentaire = new Commentaire(
        NULL,
        $namec,
        $contenue,
        $date_com,
        0,
        $id_blog
    );

    $commentaireC->ajouterCommentaire($commentaire);
    header("Location: http://localhost/gestionprojet3/view/frontOffice/pages/blog_details.php?id_blog=" . $id_blog);
    exit();
}

// === Actions sur les commentaires ===
if (isset($_GET['action']) && isset($_GET['id_com'])) {
    $id_com = $_GET['id_com'];

    switch ($_GET['action']) {
        case 'like':
            $commentaireC->likeCommentaire($id_com);
            break;

        case 'delete':
            $commentaireC->supprimerCommentaire($id_com);
            break;

        case 'edit':
            if (isset($_POST['new_content'])) {
                $commentaireC->modifierCommentaire($id_com, $_POST['new_content']);
            }
            break;
    }

    header("Location: blog_details.php?id_blog=$id_blog");
    exit;
}


// ✅ Charger les commentaires
$commentaires = $commentaireC->getCommentairesByBlog($id_blog);
?>
<!doctype html>
<html class="no-js" lang="fr">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($blog['Titre']); ?> - SmartLancer</title>
  <link rel="stylesheet" href="../../css/bootstrap.min.css">
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    body { background: #f5f7ff; font-family:'Poppins',sans-serif; }
    .hero { max-width: 900px; margin: 60px auto; }
    .hero img { width:100%; height:420px; object-fit:cover; border-radius: 12px; margin-bottom:20px; }
    .card-body { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 6px 24px rgba(0,0,0,0.06); }
    .comment { background:#fff; border-radius:12px; padding:16px; margin-bottom:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
    .comment h6 { margin-bottom:4px; color:#4a4aff; }
    .comment p { margin:0; color:#333; }
    .form-control { border-radius:10px; }
    .btn-primary { border-radius:20px; background:linear-gradient(135deg,#4a4aff,#7a5cff); border:none; }
  </style>
</head>
<body>

  <header>
    <div class="container text-center mt-3">
      <a href="blogs.php" class="btn btn-outline-primary">↩️ Retour</a>
    </div>
  </header>
<script>
function editComment(id, content) {
  const newContent = prompt("Modifier le commentaire :", content);
  if (newContent !== null && newContent.trim() !== "") {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = `blog_details.php?id_blog=<?php echo $id_blog; ?>&action=edit&id_com=${id}`;

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "new_content";
    input.value = newContent;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }
}
</script>

  <main class="container hero">
    <div class="card-body mb-5">
      <h1 style="color:#4a4aff;"><?php echo htmlspecialchars($blog['Titre']); ?></h1>
      <p class="text-muted">📅 <?php echo date('d M Y', strtotime($blog['date_creation'])); ?> | 👁️ <?php echo intval($blog['nb_view']); ?> vues</p>
      
      <?php if (!empty($blog['image'])): ?>
        <img src="../../uploads/<?php echo htmlspecialchars($blog['image']); ?>" alt="">
      <?php endif; ?>

      <div style="font-size:1.05rem; line-height:1.8; margin-top:20px;">
        <?php echo nl2br(htmlspecialchars($blog['contenue'])); ?>
      </div>
    </div>

    <!-- 🗨️ Section commentaires -->
    <div class="card-body">
      <h3 style="color:#4a4aff;">💬 Commentaires</h3>

      <?php if (empty($commentaires)): ?>
        <p class="text-muted">Aucun commentaire pour le moment. Soyez le premier à réagir !</p>
      <?php else: ?>
        <?php foreach ($commentaires as $c): ?>
  <div class="comment">
    <h6><?php echo htmlspecialchars($c['namec']); ?></h6>
    <small class="text-muted"><?php echo date('d M Y à H:i', strtotime($c['date_com'])); ?></small>
    <p><?php echo nl2br(htmlspecialchars($c['contenue'])); ?></p>

    <div style="display:flex; gap:10px;">
      <!-- 👍 Like -->
      <a href="blog_details.php?id_blog=<?php echo $id_blog; ?>&action=like&id_com=<?php echo $c['id_com']; ?>" class="btn btn-sm btn-outline-primary">
        👍 <?php echo $c['nbr_jaime']; ?>
      </a>

      <!-- ✏️ Modifier -->
      <button class="btn btn-sm btn-outline-warning" onclick="editComment(<?php echo $c['id_com']; ?>, '<?php echo htmlspecialchars($c['contenue'], ENT_QUOTES); ?>')">✏️ Modifier</button>

      <!-- 🗑️ Supprimer -->
      <a href="blog_details.php?id_blog=<?php echo $id_blog; ?>&action=delete&id_com=<?php echo $c['id_com']; ?>" 
         class="btn btn-sm btn-outline-danger"
         onclick="return confirm('Supprimer ce commentaire ?');">
         🗑️ Supprimer
      </a>
    </div>
  </div>
<?php endforeach; ?>

      <?php endif; ?>

      <!-- 📝 Formulaire d’ajout -->
     <form method="POST" action="blog_details.php?id_blog=<?php echo $blog['id_blog']; ?>">
  <input type="hidden" name="id_blog" value="<?php echo $blog['id_blog']; ?>">

  <div class="mb-3">
    <label for="namec" class="form-label">Votre nom</label>
    <input type="text" name="namec" id="namec" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="contenue" class="form-label">Votre commentaire</label>
    <textarea name="contenue" id="contenue" class="form-control" rows="4" required></textarea>
  </div>

  <button type="submit" class="btn btn-primary">Envoyer 💬</button>
</form>

    </div>
  </main>

  <footer class="text-center mt-5 mb-3">
    <p>&copy; 2025 SmartLancer. Tous droits réservés.</p>
  </footer>
</body>
</html>
