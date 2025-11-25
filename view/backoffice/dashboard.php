<?php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Backoffice - Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">

  <!-- SIDEBAR -->
  <aside class="w-64 bg-white shadow-xl fixed h-full p-6">
      <h2 class="text-2xl font-bold text-green-700 mb-10">SmartLancer Admin</h2>

      <nav class="space-y-3">
          <a href="#" class="flex items-center gap-3 p-3 rounded-lg bg-green-700 text-white font-medium">
             📊 Tableau de bord
          </a>

          <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-200 transition">
             👤 Utilisateurs
          </a>

          <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-200 transition">
             📁 Projets
          </a>

          <a href="index.php?route=logout"
             class="flex items-center gap-3 p-3 rounded-lg hover:bg-red-100 text-red-600 transition">
             🚪 Déconnexion
          </a>
      </nav>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="ml-64 w-full p-10">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-10">
      <h1 class="text-4xl font-bold text-gray-800">Gestion des utilisateurs</h1>
      <span class="text-gray-600">Bienvenue, <?= $_SESSION['name'] ?? '' ?></span>
    </div>

    <!-- TABLE -->
    <div class="bg-white shadow-lg rounded-xl p-6 border">
      <h2 class="text-xl font-semibold text-green-700 mb-4">Liste des comptes</h2>

      <div class="overflow-x-auto">
        <table class="min-w-full border-collapse w-full">
          <thead class="bg-gray-50 border-b">
            <tr class="text-left text-gray-600 uppercase text-xs">
              <th class="py-3 px-4">ID</th>
              <th class="py-3 px-4">Email</th>
              <th class="py-3 px-4">Rôle</th>
              <th class="py-3 px-4">Date</th>
              <th class="py-3 px-4 text-center">Action</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($users as $us): ?>
              <tr class="border-b hover:bg-gray-50 transition">

                <!-- ID -->
                <td class="py-3 px-4"><?= $us['id'] ?></td>

                <!-- Email -->
                <td class="py-3 px-4"><?= htmlspecialchars($us['email']) ?></td>

                <!-- Rôle -->
                <td class="py-3 px-4 text-green-700 font-semibold"><?= $us['role'] ?></td>

                <!-- Date -->
                <td class="py-3 px-4"><?= $us['created_at'] ?></td>

                <!-- ACTIONS -->
                <td class="py-3 px-4 text-center">

                  <!-- DELETE -->
                  <form action="index.php?route=delete_user"
                        method="post"
                        class="inline-block"
                        onsubmit="return confirm('Supprimer cet utilisateur ?');">

                      <input type="hidden" name="id" value="<?= $us['id'] ?>">

                      <button class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Supprimer
                      </button>
                  </form>

                  <!-- BAN / UNBAN -->
                  <form action="index.php?route=ban_user"
                        method="post"
                        class="inline-block ml-2">

                      <input type="hidden" name="id" value="<?= $us['id'] ?>">

                      <?php if (($us['banned'] ?? 0) == 0): ?>
                          <input type="hidden" name="status" value="1">
                          <button class="px-3 py-1 bg-yellow-500 text-black rounded hover:bg-yellow-600">
                              Bannir
                          </button>
                      <?php else: ?>
                          <input type="hidden" name="status" value="0">
                          <button class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
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

</body>
</html>
