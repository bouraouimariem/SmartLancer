<?php
require_once "../../config.php"; // Adapter le chemin si nécessaire
$pdo = config::getConnexion();

// 🔍 Recherche par ID
if (isset($_GET['search_id']) && $_GET['search_id'] !== "") {
    $id = intval($_GET['search_id']);
    $stmt = $pdo->prepare("SELECT * FROM publications WHERE id_pub = ?");
    $stmt->execute([$id]);
    $publications = $stmt->fetchAll();
} else {
    // Afficher toutes les publications
    $stmt = $pdo->query("SELECT * FROM publications");
    $publications = $stmt->fetchAll();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmartLancer</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link rel="icon" type="image/png" href="logo.png">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: linear-gradient(135deg, #e8f5e9, #c8e6c9); 
    }

    /* HEADER */
    header {
      background: #2e7d32;
      padding: 12px 25px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    header h2 {
      margin: 0;
      font-weight: 600;
      
    }

    .page_title {
    font-size: 20px;
    font-weight: 500;
    margin-right: 20px;
    }

    h3 {
   color:#2e7d32;
   padding-left: 10px;
   border-left: 5px solid #2e7d32;
}

    header nav a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-size: 15px;
      font-weight: 500;
      transition: 0.2s;
    }

    header nav a:hover {
      color: #60a5fa;
    }

    /* Bouton de toggle */
.toggle-btn {
  background: none;
  border: 2px solid white;
  color: white;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  font-size: 20px;
  cursor: pointer;
  transition: 0.3s;
}

/* MODE SOMBRE */
.dark-mode {
  background: linear-gradient(135deg, #1c2e21, #2e4635);
  color: #e5e5e5 !important;
}

/* Ajustements pour le contenu en sombre */
.dark-mode header {
      background: #243c2e;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.dark-mode table {
     background: #2f523eff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.dark-mode th {
  background: #0e6b20 !important;
}

.dark-mode tr:hover {
  background: #054012ff !important;
}

.dark-mode td {
  border-color: #444 !important;
}

.dark-mode h3 {
  color: white;
}
/* Badges */
.dark-mode .badge-pub {
  background: #22c55e !important;
}

.dark-mode .badge-att {
  background: #d97706 !important;
}


    .container {
      width: 90%;
      max-width: 1200px;
      margin: 25px auto;
    }

    /* SEARCH */
    .search-box {
      background: white;
      padding: 12px;
      border-radius: 6px;
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
    }
    .search-box input {
      flex: 1;
      padding: 8px;
      border: 1px solid #aaa;
      border-radius: 4px;
    }
    .search-box button {
      background: #2563eb;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    /* TABLE */
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 6px;
      overflow: hidden;
    }
    th {
      background: #2e7d32;
      color: white;
      padding: 12px;
      text-align: left;
      font-size: 14px;
    }
    td {
      padding: 12px;
      border-bottom: 1px solid #e5e7eb;
      font-size: 14px;
    }
    tr:hover {
      background: #f9fafb;
    }

    /* BADGES */
    .badge {
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      color: white;
    }
    .badge-pub {
      background: #16a34a;
    }
    .badge-att {
      background: #ca8a04;
    }

    .btn-del {
      padding: 6px 10px;
      background: #dc2626;
      border: none;
      color: white;
      border-radius: 7px;
      cursor: pointer;
    }

    @media(max-width: 600px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }
      thead { display: none; }
      tr {
        margin-bottom: 15px;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 6px;
      }
      td {
        border: none;
        display: flex;
        justify-content: space-between;
      }
      td::before {
        content: attr(data-label);
        font-weight: bold;
      }
    }
  </style>

</head>
<body>

<header>
  <h2>SmartLancer</h2>
  <div class="page_title">Gestions de publications </div>
  <button id="themeToggle" class="toggle-btn">
  🌙
</button>

</header>



<div class="container">
  <h3>Liste des Publications</h3>

  <!-- TABLE -->
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Catégorie</th>
        <th>Budget</th>
        <th>Délai</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php if (!empty($publications)) { foreach($publications as $p) { ?>
        <tr>
          <td data-label="ID"><?= $p['id_pub'] ?></td>
          <td data-label="Nom"><?= $p['nom_pub'] ?></td>

          <!-- DESCRIPTION -->
          <td data-label="Description">
            <?= htmlspecialchars($p['description']) ?>
          </td>

          <td data-label="Catégorie"><?= $p['categorie'] ?></td>
          <td data-label="Budget"><?= $p['budget'] ?> TND</td>
          <td data-label="Délai"><?= $p['delai_requise'] ?> jours</td>

          <td data-label="Status">
           <?php
if ($p['status'] === "accepte") {
    echo '<span class="badge badge-pub">Acceptée</span>';
} elseif ($p['status'] === "en cours") {
    echo '<span class="badge badge-att">En cours</span>';
} else {
    echo '<span class="badge badge-att">'.htmlspecialchars($p["status"]).'</span>';
}
?>

          </td>

          <td data-label="Action">
            <form method="POST" action="pages/delete_pub.php" onsubmit="return confirm('Supprimer cette publication ?');">
              <input type="hidden" name="id_pub" value="<?= $p['id_pub'] ?>" />
              <button class="btn-del"><i class="fa fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php }} else { ?>
        <tr><td colspan="8">Aucune publication trouvée.</td></tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<script>
document.getElementById("themeToggle").addEventListener("click", function () {
    document.body.classList.toggle("dark-mode");

    // Changer l'icône
    if (document.body.classList.contains("dark-mode")) {
        this.textContent = "🌞";
        localStorage.setItem("theme", "dark");
    } else {
        this.textContent = "🌙";
        localStorage.setItem("theme", "light");
    }
});

// 🔄 Charger le thème sauvegardé
if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark-mode");
    document.getElementById("themeToggle").textContent = "🌞";
}
</script>

</body>
</html>
