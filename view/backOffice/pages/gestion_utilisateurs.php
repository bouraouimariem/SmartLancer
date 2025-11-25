<?php 
include 'tete et pied/tete.php'; 
include '../../../controller/utilisateurC.php';

$utilisateurC = new utilisateurController();

$list = [];
$search_id = isset($_GET['recherche_id']) ? $_GET['recherche_id'] : '';
$sort_by = isset($_GET['tri']) ? $_GET['tri'] : '';

if (!empty($search_id)) {
  $user = $utilisateurC->recherche_user($search_id);
  $list = $user ? [$user] : [];
} else {
  switch ($sort_by) {
    case 'nom_asc':
      $list = $utilisateurC->list_user_nom_asc();
      break;
    case 'nom_desc':
      $list = $utilisateurC->list_user_nom_desc();
      break;
    case 'date_new':
      $list = $utilisateurC->list_user_date_new();
      break;
    case 'date_old':
      $list = $utilisateurC->list_user_date_old();
      break;
    default:
      $list = $utilisateurC->list_user_all();
      break;
  }
}
?>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <p class="card-title">Gestion des Utilisateurs</p>

        <form method="GET" action="" class="mb-3 d-flex align-items-center" style="gap: 10px;">
          <input type="text" name="recherche_id" class="form-control" placeholder="🔎 Chercher Utilisateur ID..."
            value="<?php echo htmlspecialchars($search_id); ?>"
            style="max-width: 250px; border-radius: 20px; padding-left: 20px;">

          <button type="submit" class="btn btn-primary" style="border-radius: 20px; padding: 8px 20px;">
            🔍 Recherche
          </button>

          <select name="tri" class="form-select" style="max-width: 220px; border-radius: 20px;">
            <option value="">🎯 Trier par</option>
            <option value="nom_asc" <?php echo ($sort_by == 'nom_asc') ? 'selected' : ''; ?>>⬆️ Nom croissant</option>
            <option value="nom_desc" <?php echo ($sort_by == 'nom_desc') ? 'selected' : ''; ?>>⬇️ Nom décroissant</option>
            <option value="date_new" <?php echo ($sort_by == 'date_new') ? 'selected' : ''; ?>>🆕 Plus récent</option>
            <option value="date_old" <?php echo ($sort_by == 'date_old') ? 'selected' : ''; ?>>📅 Plus ancien</option>
          </select>

          <button type="submit" class="btn btn-primary" style="border-radius: 20px; padding: 8px 20px;">
            🚀 Trier
          </button>
        </form>

        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
          <table class="table">
            <thead style="position: sticky; top: 0; background-color: white;">
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Date d’inscription</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($list as $user) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($user['id_utilisateur']); ?></td>
                  <td><?php echo htmlspecialchars($user['nom']); ?></td>
                  <td><?php echo htmlspecialchars($user['Email']); ?></td>
                  <td><?php echo htmlspecialchars($user['Role']); ?></td>
                  <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                  <td>
                    <div class="action-buttons">
                      <a href="tete et pied/delete_user.php?id_user=<?php echo $user['id_utilisateur']; ?>" class="action-btn delete-btn">
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
  }

  .delete-btn:hover {
    color: #cc2f26;
  }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php include 'tete et pied/pied.php'; ?>
