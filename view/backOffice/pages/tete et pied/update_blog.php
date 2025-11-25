<?php
include_once("../../../../controller/blogC.php");
include_once("../../../../model/blog.php");



$blogC = new BlogController();

if (isset($_GET['id_blog'])) {
    $id_blog = $_GET['id_blog'];
    $blog = $blogC->getBlogById($id_blog);
}

if (isset($_POST['modifier'])) {
    $blog = new Blog(
        $id_blog = $_POST['id_blog'],
        $_POST['titre'],
        $_POST['contenue'],
        $_POST['date_creation'],
        $_POST['image'],
        $_POST['nb_view']
    );
    
    $blogC->updateBlog($blog);
    header("Location: ../blogs.php");
    exit();
}
?>

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #e0f0ff, #f0e8ff); min-height: 100vh;">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card border-0 shadow-lg" style="border-radius: 25px;">
        <div class="card-body p-5">
          <h3 class="text-center mb-4" style="color: #4a4aff; font-weight: 700;">
            ✏️ Modifier un article de blog
          </h3>

          <?php if ($blog) { ?>
            <form method="POST">
              <input type="hidden" name="id_blog" value="<?php echo $blog['id_blog']; ?>">

              <div class="form-group mb-4">
                <label class="fw-bold">Titre de l’article</label>
                <input type="text" name="titre" class="form-control shadow-sm"
                  value="<?php echo htmlspecialchars($blog['Titre']); ?>"
                  required style="border-radius: 15px;">
              </div>

              <div class="form-group mb-4">
                <label class="fw-bold">Contenu</label>
                <textarea name="contenue" class="form-control shadow-sm" rows="6"
                  style="border-radius: 15px; resize: none;"><?php echo htmlspecialchars($blog['contenue']); ?></textarea>
              </div>

              <div class="form-group mb-4">
                <label class="fw-bold">Image (nom de fichier)</label>
                <input type="text" name="image" class="form-control shadow-sm"
                  value="<?php echo htmlspecialchars($blog['image']); ?>"
                  style="border-radius: 15px;">
              </div>

              <div class="row">
                <div class="col-md-6 mb-4">
                  <label class="fw-bold">Date de création</label>
                  <input type="date" name="date_creation" class="form-control shadow-sm"
                    value="<?php echo htmlspecialchars($blog['date_creation']); ?>"
                    required style="border-radius: 15px;">
                </div>
                <div class="col-md-6 mb-4">
                  <label class="fw-bold">Nombre de vues</label>
                  <input type="number" name="nb_view" class="form-control shadow-sm"
                    value="<?php echo htmlspecialchars($blog['nb_view']); ?>"
                    style="border-radius: 15px;">
                </div>
              </div>

              <div class="d-flex justify-content-between mt-4">
                <a href="../blogs.php" class="btn btn-light shadow-sm px-4 py-2"
                  style="border-radius: 30px; border: 1px solid #ccc;">
                  ❌ Annuler
                </a>
                <button type="submit" name="modifier" class="btn text-white px-4 py-2"
                  style="border-radius: 30px; background: linear-gradient(135deg, #4a4aff, #8b5cff); border: none;">
                  💾 Enregistrer les modifications
                </button>
              </div>
            </form>
          <?php } else { ?>
            <p class="text-center text-danger fw-bold mt-4">❌ Article introuvable.</p>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .form-control {
    border: 1px solid #d4d4f7;
    transition: all 0.3s ease-in-out;
  }

  .form-control:focus {
    border-color: #6a5cff;
    box-shadow: 0 0 8px rgba(106, 92, 255, 0.3);
  }

  label {
    color: #333;
  }

  .card {
    background-color: #fff;
  }

  .btn-light:hover {
    background-color: #f7f7f7;
  }

  .btn.text-white:hover {
    background: linear-gradient(135deg, #3e3ed9, #7d44ff);
  }
</style>

<?php include 'pied.php'; ?>
