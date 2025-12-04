  <?php 
  include 'tete et pied/tete.php';
  include_once '../../../controller/blogC.php';
  $blogC = new BlogController();

  $search_id = isset($_GET['recherche_id']) ? $_GET['recherche_id'] : '';
  $sort_by = isset($_GET['tri']) ? $_GET['tri'] : '';

  if (!empty($search_id)) {
    $blogs = $blogC->searchBlogById($search_id);
  } else {
    switch ($sort_by) {
      case 'titre_asc':
        $blogs = $blogC->listBlogTitreAsc();
        break;
      case 'titre_desc':
        $blogs = $blogC->listBlogTitreDesc();
        break;
      case 'date_new':
        $blogs = $blogC->listBlogDateNew();
        break;
      case 'date_old':
        $blogs = $blogC->listBlogDateOld();
        break;
      default:
        $blogs = $blogC->listBlogs();
        break;
    }
  }
  ?>

  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title">📰 Gestion des Blogs</p>

          <form method="GET" action="" class="mb-3 d-flex align-items-center" style="gap: 10px;">
            <input type="text" name="recherche_id" class="form-control" placeholder="Chercher Blog ID..."
              value="<?php echo htmlspecialchars($search_id); ?>"
              style="max-width: 250px; border-radius: 20px; padding-left: 20px;">

            <button type="submit" class="btn btn-primary" style="border-radius: 20px; padding: 8px 20px;">
              Recherche
            </button>

            <select name="tri" class="form-select" style="max-width: 220px; border-radius: 20px;">
              <option value="">Trier par</option>
              <option value="titre_asc" <?php echo ($sort_by == 'titre_asc') ? 'selected' : ''; ?>>⬆️ Titre croissant</option>
              <option value="titre_desc" <?php echo ($sort_by == 'titre_desc') ? 'selected' : ''; ?>>⬇️ Titre décroissant</option>
              <option value="date_new" <?php echo ($sort_by == 'date_new') ? 'selected' : ''; ?>>🆕 Plus récent</option>
              <option value="date_old" <?php echo ($sort_by == 'date_old') ? 'selected' : ''; ?>>📅 Plus ancien</option>
            </select>

            <button type="submit" class="btn btn-primary" style="border-radius: 20px; padding: 8px 20px;">
              Trier
            </button>

            <a href="add_blog.php" class="btn btn-success" style="border-radius: 20px; padding: 8px 20px;">
              ➕ Ajouter un Blog
            </a>
          </form>

          <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table">
              <thead style="position: sticky; top: 0; background-color: white;">
                <tr>
                  <th>ID</th>
                  <th>Titre</th>
                  <th>Contenu</th>
                  <th>Date</th>
                  <th>Image</th>
                  <th>Vues</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($blogs as $b) { ?>
                  <tr>
                    <td><?php echo $b['id_blog']; ?></td>
                    <td><?php echo htmlspecialchars($b['Titre']); ?></td>
                    <td><?php echo substr(htmlspecialchars($b['contenue']), 0, 50) . '...'; ?></td>
                    <td><?php echo $b['date_creation']; ?></td>
                    <td><img src="../../uploads/<?php echo htmlspecialchars($b['image']); ?>" width="80"></td>
                    <td><?php echo $b['nb_view']; ?></td>
                    <td>
                      <div class="action-buttons">
                        <a href="tete%20et%20pied/delete_blog.php?id_blog=<?php echo $b['id_blog']; ?>" class="action-btn delete-btn"
                          onclick="return confirm('Voulez-vous vraiment supprimer ce blog ?')">
                          
                          <i class="fa-regular fa-trash-can"></i>
                        </a>
                        <a href="tete et pied/update_blog.php?id_blog=<?php echo $b['id_blog']; ?>" class="action-btn edit-btn">
    <i class="fa-regular fa-pen-to-square"></i>
  </a>

                      </div>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
  <hr>
  <hr>
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title">💬 Gestion des Commentaires</p>

          <?php
          include_once '../../../controller/commentaireC.php';
          $commentaireC = new CommentaireC();
          $listeCommentaires = $commentaireC->afficherCommentaires();
          ?>

          <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table">
              <thead style="position: sticky; top: 0; background-color: white;">
                <tr>
                  <th>ID</th>
                  <th>Auteur</th>
                  <th>Contenu</th>
                  <th>Blog associé</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($listeCommentaires as $c) { ?>
                  <tr>
    <td><?= $c['id_com']; ?></td>
          <td><?= htmlspecialchars($c['namec']); ?></td>
          <td><?= htmlspecialchars($c['contenue']); ?></td>
          <td><?= htmlspecialchars($c['id_blog']); ?></td>
          <td><?= $c['date_com']; ?></td>
                    <td>
                      <div class="action-buttons">
                        
                     <a href="tete%20et%20pied/delete_commentaire.php?id_com=<?= $c['id_com']; ?>" 
                        class="action-btn delete-btn"
                        onclick="return confirm('Supprimer ce commentaire ?');">
                        <i class="fa-regular fa-trash-can"></i>
                     </a>

                      </div>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

  <style>
    .action-buttons {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
    }

    .delete-btn {
      color: #ff3b30;
      transition: 0.2s;
    }

    .delete-btn:hover {
      color: #cc2f26;
    }

    .edit-btn {
      color: #007bff;
      transition: 0.2s;
    }

    .edit-btn:hover {
      color: #0056b3;
    }

    .card-title {
      font-size: 20px;
      font-weight: 600;
    }
  </style>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">




  <?php include 'tete et pied/pied.php'; ?>
