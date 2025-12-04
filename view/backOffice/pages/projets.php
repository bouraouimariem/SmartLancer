<?php 
include 'tete et pied/tete.php';
include '../../../controller/publicationC.php';
$publicationC = new publicationController();

$list = [];
$search_id = isset($_GET['recherche_id']) ? $_GET['recherche_id'] : '';
$sort_by = isset($_GET['tri']) ? $_GET['tri'] : '';

if (!empty($search_id)) {
  $pub = $publicationC->recherche_pub($search_id);
  $list = $pub ? [$pub] : [];
} else {
  switch ($sort_by) {
    case 'budget_asc':
      $list = $publicationC->list_pub_budget_asc();
      break;
    case 'budget_desc':
      $list = $publicationC->list_pub_budget_desc();
      break;
    case 'date_new':
      $list = $publicationC->list_pub_date_new();
      break;
    case 'date_old':
      $list = $publicationC->list_pub_date_old();
      break;
    default:
      $list = $publicationC->list_pub_all();
      break;
  }
}
$search_id_propo = isset($_GET['recherche_id_propo']) ? $_GET['recherche_id_propo'] : '';
$sort_by_propo = isset($_GET['tri_propo']) ? $_GET['tri_propo'] : '';
?>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <p class="card-title">Publication</p>

        <form method="GET" action="" class="mb-3 d-flex align-items-center" style="gap: 10px;">
          <input type="text" name="recherche_id" class="form-control" placeholder="Chercher Publication ID..."
            value="<?php echo htmlspecialchars($search_id); ?>"
            style="max-width: 250px; border-radius: 20px; padding-left: 20px; padding-top: 10px;padding-bottom: 10px; ">

          <button type="submit" class="btn btn-primary"
            style="border-radius: 20px; padding: 8px 20px; transition: 0.3s;">
            Recherche
          </button>

          <select name="tri" class="form-select" style="max-width: 220px; border-radius: 20px; transition: 0.3s;">
            <option value="">Trier par</option>
            <option value="budget_asc" <?php echo ($sort_by == 'budget_asc') ? 'selected' : ''; ?>>
              ⬆️ Budget croissant</option>
            <option value="budget_desc" <?php echo ($sort_by == 'budget_desc') ? 'selected' : ''; ?>>
              ⬇️ Budget décroissant</option>
            <option value="date_new" <?php echo ($sort_by == 'date_new') ? 'selected' : ''; ?>>
              🆕 Plus récent</option>
            <option value="date_old" <?php echo ($sort_by == 'date_old') ? 'selected' : ''; ?>>
              📅 Plus ancien</option>
          </select>

          <button type="submit" class="btn btn-primary"
            style="border-radius: 20px; padding: 8px 20px; transition: 0.3s;">
            Trier
          </button>
        </form>

        <div class="table-responsive" style="max-height: 350px; overflow-y: auto; position: relative;">
          <table id="recent-purchases-listing" class="table">
            <thead style="position: sticky; top: 0; background-color: white; z-index: 100;">
              <tr>
                <th>Id_pub</th>
                <th>Id_user</th>
                <th>Nom_projet</th>
                <th>Description</th>
                <th>Budget (dt)</th>
                <th>Delai_requise</th>
                <th>Date_publication</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <?php foreach ($list as $pub) { ?>
              <tbody>

                <tr>
                  <td><?php echo $pub['id_pub']; ?></td>
                  <td><?php echo $pub['id_user']; ?></td>
                  <td><?php echo $pub['nom_pub']; ?></td>
                  <td><?php echo $pub['description']; ?></td>
                  <td><?php echo $pub['budget']; ?></td>
                  <td><?php echo $pub['delai_requise']; ?></td>
                  <td><?php echo $pub['date_pub']; ?></td>
<td>
  <?php
    $status = strtolower($pub['status']);
    $class = '';

    if ($status == 'accepté' || $status == 'accepte') {
        $class = 'status-accepted';
    } elseif ($status == 'refusé' || $status == 'refuse') {
        $class = 'status-refused';
    } elseif ($status == 'en cours') {
        $class = 'status-pending';
    } else {
        $class = 'status-default';
    }
  ?>
  <span class="status-badge <?php echo $class; ?>">
    <?php echo $pub['status']; ?>
  </span>
</td>
                  <td>
                    <div class="action-buttons">
                      <a href="tete et pied/delete_publication.php?id_pub=<?php echo $pub['id_pub']; ?>"
                        class="action-btn delete-btn">
                        <i class="fa-regular fa-trash-can"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              </tbody>
            <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <p class="card-title">Propositions</p>
        <form method="GET" action="" class="mb-3 d-flex align-items-center" style="gap: 10px;">
          <input type="text" name="recherche_id_propo" class="form-control" placeholder="Chercher Propositon ID..."
            value="<?php echo htmlspecialchars($search_id_propo); ?>"
           style="max-width: 250px; border-radius: 20px; padding-left: 20px; padding-top: 10px;padding-bottom: 10px; ">

          <button type="submit" class="btn btn-primary"
            style="border-radius: 20px; padding: 8px 20px; transition: 0.3s;">
            Recherche
          </button>

          <select name="tri_propo" class="form-select" style="max-width: 220px; border-radius: 20px; transition: 0.3s;">
            <option value="">Trier par</option>
            <option value="budget_asc" <?php echo ($sort_by_propo == 'motant_asc') ? 'selected' : ''; ?>>
              ⬆️ montant croissant</option>
            <option value="budget_desc" <?php echo ($sort_by_propo == 'montant_desc') ? 'selected' : ''; ?>>
              ⬇️ montant décroissant</option>
            <option value="date_new" <?php echo ($sort_by_propo == 'propo_new') ? 'selected' : ''; ?>>
              🆕 Plus récent</option>
            <option value="date_old" <?php echo ($sort_by_propo == 'propo_old') ? 'selected' : ''; ?>>
              📅 Plus ancien</option>
          </select>
          <button type="submit" class="btn btn-primary"
            style="border-radius: 20px; padding: 8px 20px; transition: 0.3s;">
            Trier
          </button>
        </form>
        <div class="table-responsive" style="max-height: 350px; overflow-y: auto; position: relative;">
          <table id="recent-purchases-listing" class="table">
            <thead style="position: sticky; top: 0; background-color: white; z-index: 100;">
              <tr>
                <th>Id_Proposition</th>
                <th>Id_Utilisateur</th>
                <th>Id_Projet</th>
                <th>commentaire</th>
                <th>Montant_propose (dt)</th>
                <th>delai_estime</th>
                <th>Date_proposition</th>
                <th>status</th>
                <th colspan="2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php include '../../../controller/propositionC.php';
              $propositionC = new propositionController();
              $list_propo = [];
             
              
              if (!empty($search_id_propo)) {
                $propo = $propositionC->recherche_propo($search_id_propo);
                $list_propo = $propo ? [$propo] : [];
              } else {
                switch ($sort_by_propo) {
                  case 'budget_asc':
                    $list_propo = $propositionC->list_propo_montant_asc();
                    break;
                  case 'budget_desc':
                    $list_propo = $propositionC->list_propo_montant_desc();
                    break;
                  case 'date_new':
                    $list_propo = $propositionC->list_propo_date_new();
                    break;
                  case 'date_old':
                    $list_propo = $propositionC->list_propo_date_old();
                    break;
                  default:
                    $list_propo = $propositionC->list_propo();
                    break;
                }
              }    
              foreach ($list_propo as $propo) { ?>
                <tr>
                  <td><?php echo $propo['id_propo']; ?></td>
                  <td><?php echo $propo['id_user']; ?></td>
                  <td><?php echo $propo['id_pub']; ?></td>
                  <td><?php echo $propo['commentaire']; ?></td>
                  <td><?php echo $propo['montant_propo']; ?></td>
                  <td><?php echo $propo['delai_estime']; ?></td>
                  <td><?php echo $propo['date_propo']; ?></td>
<td>
  <?php
    $status = strtolower($propo['status']);
    $class = '';

    if ($status == 'accepté' || $status == 'accepte') {
        $class = 'status-accepted';
    } elseif ($status == 'refusé' || $status == 'refuse') {
        $class = 'status-refused';
    } elseif ($status == 'en cours') {
        $class = 'status-pending';
    } else {
        $class = 'status-default';
    }
  ?>
  <span class="status-badge <?php echo $class; ?>">
    <?php echo $propo['status']; ?>
  </span>
</td>
                  <td>
                    <div class="action-buttons">
                      <a href="tete et pied/delete_proposition.php?id_propo=<?php echo $propo['id_propo']; ?>"
                        class="action-btn delete-btn">
                        <i class="fa-regular fa-trash-can"></i>
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

  element.style {
  
    border-radius: 20px;
    padding-left: 20px;
}
  .status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
  }

 .status-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
  color: white;
  min-width: 80px;
  text-align: center;
}

/* ✅ Accepté = Vert */
.status-accepted {
  background-color: #28a745;
}

/* ❌ Refusé = Rouge */
.status-refused {
  background-color: #dc3545;
}

/* ⏳ En cours = Jaune */
.status-pending {
  background-color: #ffc107;
  color: #000;
}

/* ⚪ Autre = Gris */
.status-default {
  background-color: #6c757d;
}

  /* Action Buttons */
  .action-buttons {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
  }

  .action-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #666;
    font-size: 16px;
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