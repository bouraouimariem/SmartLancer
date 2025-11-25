<?php
include_once '../../../controller/blogC.php';
include_once '../../../model/blog.php';

$blogC = new BlogController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $Titre = isset($_POST['Titre']) ? trim($_POST['Titre']) : null;
  $contenue = isset($_POST['Contenue']) ? trim($_POST['Contenue']) : null;
  $date_creation = date('Y-m-d');
  $nb_view = 0;

  // Gestion de l'image
  $image_name = '';
  if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image_name = basename($_FILES['image']['name']);
    $upload_dir = __DIR__ . "/../../uploads/"; // chemin absolu
    if (!file_exists($upload_dir)) {
      mkdir($upload_dir, 0777, true); // crée le dossier s’il n’existe pas
    }
    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
  }

  // Création d’un nouvel objet blog
  $blog = new Blog(null, $Titre, $contenue, $date_creation, $image_name, $nb_view);
  $blogC->addBlog($blog);

  header('Location: blogs.php');
  exit;
}
?>

<?php include 'tete et pied/tete.php'; ?>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title text-center mb-4">📝 Ajouter un Blog</h4>

        <form method="POST" enctype="multipart/form-data">
          <div class="form-group mb-3">
            <label for="Titre">Titre :</label>
            <input type="text" name="Titre" id="Titre" class="form-control" required>
          </div>

          <div class="form-group mb-3">
            <label for="Contenue">Contenu :</label>
            <textarea name="Contenue" id="Contenue" class="form-control" rows="5" required></textarea>
          </div>

          <div class="form-group mb-3">
            <label for="image">Image :</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
          </div>

          <div class="d-flex justify-content-between">
            <a href="blogs.php" class="btn btn-secondary" style="border-radius: 20px;">↩️ Retour</a>
            <button type="submit" class="btn btn-success" style="border-radius: 20px;">💾 Publier</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'tete et pied/pied.php'; ?>
