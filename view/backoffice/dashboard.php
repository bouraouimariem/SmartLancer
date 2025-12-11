<?php
// dashboard.php
// $users doit être fourni par le controller (BackofficeController::index)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Backoffice - Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* Fade-in animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in { animation: fadeIn 0.8s ease-out forwards; }

    /* Button 3D Hover */
    .btn-3d { transition: 0.25s ease; }
    .btn-3d:hover { transform: translateY(-5px) scale(1.05); box-shadow: 0 15px 25px rgba(0,0,0,0.2); }

    /* Row animation */
    .row-anim { transition: 0.25s ease; }
    .row-anim:hover { transform: scale(1.01); background-color: #f5f5f5; }

    /* Sidebar glass hover */
    .sidebar:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.2); transform: scale(1.02); transition: 0.3s; }

    /* Small helpers for ban button states */
    .ban-btn { background: #f59e0b; color: #000; }
    .unban-btn { background: #16a34a; color: #fff; }

    /* Hide element utility */
    .hidden-el { display: none !important; }

    /* Smooth reveal for delete button */
    .reveal {
      display: inline-block;
      opacity: 0;
      transform: translateY(-6px);
      animation: revealAnim 260ms ease forwards;
    }
    @keyframes revealAnim {
      to { opacity: 1; transform: translateY(0); }
    }

    /* Stat cards */
    .stat-card { border-radius: 12px; padding: 1rem; }
    .badge-active { background: #16a34a; color: #fff; padding: 0.25rem 0.5rem; border-radius: 999px; font-weight:600; }
    .badge-banned { background: #dc2626; color: #fff; padding: 0.25rem 0.5rem; border-radius: 999px; font-weight:600; }
    .small-muted { color: #6b7280; font-size: 0.85rem; }

    /* Status text badge in table */
    .status-banned { background: #fee2e2; color: #991b1b; padding: .25rem .5rem; border-radius: 999px; font-weight:600; display:inline-block; }
    .status-active { background: #ecfdf5; color: #065f46; padding: .25rem .5rem; border-radius: 999px; font-weight:600; display:inline-block; }

    /* Responsive tweaks */
    @media (max-width: 900px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    }
    @media (max-width: 640px) {
      .stats-grid { grid-template-columns: 1fr; }
      .sidebar { display: none; } /* Hide sidebar on very small screens for simplicity */
      main { margin-left: 0 !important; }
    }
  </style>

</head>

<body class="bg-gray-100 min-h-screen flex fade-in">

  <!-- SIDEBAR -->
  <aside class="sidebar w-64 bg-white shadow-xl fixed h-full p-6">
      <h2 class="text-2xl font-bold text-green-700 mb-10">SmartLancer Admin</h2>

      <nav class="space-y-3">
          <a href="#" class="flex items-center gap-3 p-3 rounded-lg bg-green-700 text-white font-medium btn-3d">
             📊 Tableau de bord
          </a>

          <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-200 shadow btn-3d">
             👤 Utilisateurs
          </a>

          <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-200 shadow btn-3d">
             📁 Projets
          </a>

          <a href="index.php?route=logout"
             class="flex items-center gap-3 p-3 rounded-lg hover:bg-red-100 text-red-600 shadow btn-3d">
             🚪 Déconnexion
          </a>
      </nav>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="ml-64 w-full p-10 fade-in">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-4xl font-bold text-gray-800">Gestion des utilisateurs</h1>
        <div class="small-muted mt-1">Bienvenue, <?= htmlspecialchars($_SESSION['name'] ?? '') ?></div>
      </div>

      
    </div>

    <?php
      // Calculate statistics from $users array
      $totalUsers = 0;
      $totalFreelancers = 0;
      $totalClients = 0;
      $totalAdmins = 0;
      $totalBanned = 0;

      if (!empty($users) && is_array($users)) {
        foreach ($users as $u) {
          $totalUsers++;
          $role = strtolower($u['role'] ?? '');
          if ($role === 'freelancer') $totalFreelancers++;
          if ($role === 'client') $totalClients++;
          if ($role === 'admin') $totalAdmins++;
          $bannedFlag = (int)($u['banned'] ?? $u['is_banned'] ?? 0);
          if ($bannedFlag === 1) $totalBanned++;
        }
      }
    ?>

    <!-- STATS CARDS -->
    <div class="grid stats-grid grid-cols-4 gap-4 mb-6">
      <div class="stat-card bg-white shadow p-4">
        <div class="text-sm text-gray-500">Utilisateurs</div>
        <div class="text-2xl font-bold"><?= (int)$totalUsers ?></div>
        <div class="small-muted mt-1">Total enregistré</div>
      </div>

      <div class="stat-card bg-white shadow p-4">
        <div class="text-sm text-gray-500">Freelancers</div>
        <div class="text-2xl font-bold"><?= (int)$totalFreelancers ?></div>
        <div class="small-muted mt-1">Profils actifs</div>
      </div>

      <div class="stat-card bg-white shadow p-4">
        <div class="text-sm text-gray-500">Clients</div>
        <div class="text-2xl font-bold"><?= (int)$totalClients ?></div>
        <div class="small-muted mt-1">Demandes & projets</div>
      </div>

      <div class="stat-card bg-white shadow p-4">
        <div class="text-sm text-gray-500">Bannis</div>
        <div class="text-2xl font-bold text-red-600"><?= (int)$totalBanned ?></div>
        <div class="small-muted mt-1">Utilisateurs bannis</div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white shadow-lg rounded-xl p-6 border">
      <h2 class="text-xl font-semibold text-green-700 mb-4">Liste des comptes</h2>

      <div class="overflow-x-auto">
        <!-- FORMULAIRE RECHERCHE / FILTRE / TRI -->
<form method="GET" class="flex flex-wrap gap-3 mb-6 items-end">
  <input type="hidden" name="route" value="backoffice">
  
  <div>
    <label class="block text-sm font-medium text-gray-700">Recherche</label>
    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Nom ou Email"
           class="mt-1 px-3 py-2 border rounded-lg">
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Filtrer par rôle</label>
    <select name="role" class="mt-1 px-3 py-2 border rounded-lg">
      <option value="">Tous</option>
      <option value="Admin" <?= (($_GET['role'] ?? '') === 'Admin') ? 'selected' : '' ?>>Admin</option>
      <option value="Client" <?= (($_GET['role'] ?? '') === 'Client') ? 'selected' : '' ?>>Client</option>
      <option value="Freelancer" <?= (($_GET['role'] ?? '') === 'Freelancer') ? 'selected' : '' ?>>Freelancer</option>
    </select>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Trier par</label>
    <select name="sort" class="mt-1 px-3 py-2 border rounded-lg">
      <option value="id" <?= (($_GET['sort'] ?? '') === 'id') ? 'selected' : '' ?>>ID</option>
      <option value="email" <?= (($_GET['sort'] ?? '') === 'email') ? 'selected' : '' ?>>Email</option>
      <option value="role" <?= (($_GET['sort'] ?? '') === 'role') ? 'selected' : '' ?>>Rôle</option>
      <option value="created_at" <?= (($_GET['sort'] ?? '') === 'created_at') ? 'selected' : '' ?>>Date</option>
    </select>
  </div>

  <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 btn-3d">Appliquer</button>
</form>

        <table class="min-w-full border-collapse w-full">
          <thead class="bg-gray-50 border-b">
            <tr class="text-left text-gray-600 uppercase text-xs">
              <th class="py-3 px-4">ID</th>
              <th class="py-3 px-4">Email</th>
              <th class="py-3 px-4">Rôle</th>
              <th class="py-3 px-4">Date</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4 text-center">Action</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($users as $us):
                // Normalize and read banned flag (support different DB column names)
                $isBanned = ((int)($us['banned'] ?? $us['is_banned'] ?? 0)) === 1;
            ?>
              <tr class="border-b row-anim" data-user-row="<?= (int)$us['id'] ?>">

                <td class="py-3 px-4"><?= htmlspecialchars($us['id']) ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($us['email']) ?></td>
                <td class="py-3 px-4 text-green-700 font-semibold"><?= htmlspecialchars($us['role']) ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($us['created_at']) ?></td>

                <td class="py-3 px-4">
                  <?php if ($isBanned): ?>
                    <span class="status-banned">Banni</span>
                  <?php else: ?>
                    <span class="status-active">Actif</span>
                  <?php endif; ?>
                </td>

                <td class="py-3 px-4 text-center space-x-2">

                  <!-- DELETE (visible only if banned in current UI) -->
                  <span id="delete-wrapper-<?= (int)$us['id'] ?>" class="<?= $isBanned ? 'reveal' : 'hidden-el' ?>">
                    <form action="index.php?route=delete_user"
                          method="post"
                          class="inline-block delete-form"
                          onsubmit="return confirm('Supprimer cet utilisateur ?');">
                        <input type="hidden" name="id" value="<?= (int)$us['id'] ?>">
                        <button class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 btn-3d">
                          Supprimer
                        </button>
                    </form>
                  </span>

                  <!-- BAN / UNBAN -->
                  <form action="index.php?route=ban_user"
                        method="post"
                        class="inline-block ml-2 ban-form"
                        data-user-id="<?= (int)$us['id'] ?>">
                      <input type="hidden" name="id" value="<?= (int)$us['id'] ?>">
                      <input type="hidden" name="status" value="<?= $isBanned ? 0 : 1 ?>" class="status-input">

                      <?php if (!$isBanned): ?>
                          <button type="submit" class="px-3 py-1 ban-btn rounded hover:bg-yellow-600 btn-3d" data-state="ban">
                              Bannir
                          </button>
                      <?php else: ?>
                          <button type="submit" class="px-3 py-1 unban-btn rounded hover:bg-green-700 btn-3d" data-state="unban">
                              Débannir
                          </button>
                      <?php endif; ?>

                  </form>

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>

        </table>
        
      </div>
    </div>

  </main>

  <script>
    // When admin clicks the ban form submit, show the delete button instantly for that row and
    // change the appearance/text of the ban button (UX immediate feedback), then let the form submit.
    document.addEventListener('DOMContentLoaded', function () {
      // attach submit listeners to all ban forms
      document.querySelectorAll('.ban-form').forEach(function(form){
        form.addEventListener('submit', function (e) {
          // find user id for this form
          const userId = form.getAttribute('data-user-id');
          if (!userId) return; // fallback

          // find the delete wrapper for this user
          const deleteWrap = document.getElementById('delete-wrapper-' + userId);
          const statusInput = form.querySelector('.status-input');
          const btn = form.querySelector('button');

          // Toggle UI immediately (before page reload) depending on current state
          const currentState = btn.getAttribute('data-state'); // 'ban' or 'unban' if present
          if (currentState === 'ban' || currentState === null) {
            // We are going to ban -> show delete button and change ban to deban style/text
            if (deleteWrap) {
              deleteWrap.classList.remove('hidden-el');
              deleteWrap.classList.add('reveal');
            }
            if (btn) {
              btn.classList.remove('ban-btn');
              btn.classList.add('unban-btn');
              btn.textContent = 'Débannir';
              btn.setAttribute('data-state', 'unban');
              // update hidden input status to 1 (ban) so backend receives ban request
              if (statusInput) statusInput.value = 1;
            }
          } else {
            // current state is 'unban' -> we'll unban: hide the delete button
            if (deleteWrap) {
              deleteWrap.classList.add('hidden-el');
              deleteWrap.classList.remove('reveal');
            }
            if (btn) {
              btn.classList.remove('unban-btn');
              btn.classList.add('ban-btn');
              btn.textContent = 'Bannir';
              btn.setAttribute('data-state', 'ban');
              if (statusInput) statusInput.value = 0;
            }
          }

          // Let the form continue to submit (page may reload after server response)
          // We do not call preventDefault here: we want the controller to run.
          // If you prefer AJAX, preventDefault and submit via fetch instead.
        });
      });
    });
  </script>

</body>
</html>
