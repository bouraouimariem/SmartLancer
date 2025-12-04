<?php 
require_once __DIR__ . '/../../model/database.php';
require_once __DIR__ . '/../../model/avis.php';
require_once __DIR__ . '/../../model/reponse.php';

$pdo = (new Database())->getConnection();
$avisModel = new Avis($pdo);
$reponseModel = new Reponse($pdo);

// Supprimer un avis
if (isset($_GET['delete_id'])) {
    $avisModel->deleteAvis($_GET['delete_id']);
    header("Location: avisadmin.php");
    exit;
}

// Supprimer une réponse (admin)
if (isset($_GET['delete_response_id'])) {
    $id = (int)$_GET['delete_response_id'];
    if ($id > 0) {
        $reponseModel->deleteById($id);
    }
    header("Location: avisadmin.php");
    exit;
}

// Traitement des filtres/recherche pour AVIS
$avis_sort_by = $_GET['avis_sort_by'] ?? 'recent';
$avis_min_note = isset($_GET['avis_min_note']) ? (int)$_GET['avis_min_note'] : 1;
$avis_max_note = isset($_GET['avis_max_note']) ? (int)$_GET['avis_max_note'] : 5;
$avis_search_keyword = $_GET['avis_search'] ?? '';
$avis_page = isset($_GET['avis_page']) ? max(1, (int)$_GET['avis_page']) : 1;
$avis_limit = 15;
$avis_offset = ($avis_page - 1) * $avis_limit;

// Traitement des filtres/recherche pour REPONSES
$reponse_search_keyword = isset($_GET['reponse_search']) ? trim($_GET['reponse_search']) : '';
$reponse_visible_only = isset($_GET['reponse_visible_only']) ? ($_GET['reponse_visible_only'] === '1') : false;
$reponse_sort_by = isset($_GET['reponse_sort_by']) ? $_GET['reponse_sort_by'] : 'recent';
$reponse_page = isset($_GET['reponse_page']) ? max(1, (int)$_GET['reponse_page']) : 1;
$reponse_limit = 15;
$reponse_offset = ($reponse_page - 1) * $reponse_limit;

// Récupérer les avis filtrés ou recherchés
$avis_stats = $avisModel->getStatistics();
if (!empty($avis_search_keyword)) {
    $allAvis = $avisModel->searchAvis($avis_search_keyword, $avis_limit, $avis_offset);
    $total_avis = $avisModel->countSearchResults($avis_search_keyword);
} else {
    $allAvis = $avisModel->getAvisByFilters($avis_sort_by, $avis_min_note, $avis_max_note, $avis_limit, $avis_offset);
    $total_avis = $avisModel->countAvisByFilters($avis_min_note, $avis_max_note);
}

$avis_total_pages = ceil($total_avis / $avis_limit);

// Récupérer les réponses filtrées ou recherchées
$reponse_stats = $reponseModel->getStatistics();
if (!empty($reponse_search_keyword)) {
    $allResponses = $reponseModel->searchReponses($reponse_search_keyword, $reponse_visible_only, $reponse_limit, $reponse_offset);
    $total_reponses = $reponseModel->countSearchResults($reponse_search_keyword, $reponse_visible_only);
} else {
    $allResponses = $reponseModel->getReponsesByFilters($reponse_sort_by, $reponse_visible_only, $reponse_limit, $reponse_offset);
    $total_reponses = $reponseModel->countReponsesByFilters($reponse_visible_only);
}

$reponse_total_pages = ceil($total_reponses / $reponse_limit);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - Gestion des Avis</title>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --primary: #075e3a;
        --primary-dark: #054a2a;
        --primary-light: #e8f5e9;
        --success: #28a745;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #17a2b8;
        --gray-50: #fafafa;
        --gray-100: #f5f5f5;
        --gray-200: #eeeeee;
        --gray-300: #e0e0e0;
        --gray-600: #757575;
        --gray-dark: #333;
        --border-radius: 10px;
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
        --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.12);
        --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.15);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #f3f6f8 0%, #e8f0f5 100%);
        color: var(--gray-dark);
        min-height: 100vh;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 260px;
        height: 100%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff;
        display: flex;
        flex-direction: column;
        padding-top: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        transition: transform 0.3s ease;
        transform: translateX(0);
    }

    .sidebar.hidden {
        transform: translateX(-100%);
    }

    .toggle-sidebar-btn {
        position: fixed;
        top: 20px;
        left: 20px;
        width: 45px;
        height: 45px;
        background: var(--primary) !important;
        border: none !important;
        border-radius: 8px !important;
        color: white !important;
        font-size: 24px !important;
        cursor: pointer !important;
        z-index: 10001 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.3s ease !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }

    .toggle-sidebar-btn:hover {
        background: var(--primary-dark) !important;
        transform: scale(1.1) !important;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important;
    }

    .sidebar h2 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 30px;
        text-align: center;
        letter-spacing: 1px;
    }

    .sidebar img.logo {
        height: 80px;
        margin-bottom: 16px;
        opacity: 0.9;
    }

    .sidebar a {
        text-decoration: none;
        color: rgba(255, 255, 255, 0.85);
        padding: 12px 18px;
        margin: 6px 12px;
        text-align: center;
        border-radius: 8px;
        transition: var(--transition);
        font-weight: 500;
        font-size: 13px;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        transform: translateX(4px);
    }

    header {
        margin-left: 260px;
        background: #fff;
        box-shadow: var(--shadow-md);
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 100;
        transition: margin-left 0.3s ease;
    }

    header.sidebar-hidden {
        margin-left: 0;
    }

    header h1 {
        color: var(--primary);
        font-size: 26px;
        font-weight: 700;
    }

    header input {
        padding: 10px 16px;
        border: 1.5px solid var(--gray-300);
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        transition: var(--transition);
    }

    header input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(7, 94, 58, 0.15);
    }

    main {
        margin-left: 260px;
        padding: 30px 40px;
        transition: margin-left 0.3s ease;
    }

    main.sidebar-hidden {
        margin-left: 0;
    }

    nav {
        transition: margin-left 0.3s ease;
    }

    main.sidebar-hidden ~ nav,
    nav.sidebar-hidden {
        margin-left: 0;
    }

    .card {
        background: #fff;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        padding: 28px;
        margin-bottom: 24px;
        border-top: 4px solid var(--primary);
        transition: var(--transition);
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .card h2 {
        color: var(--primary);
        margin-bottom: 20px;
        font-size: 22px;
        font-weight: 700;
    }

    .card h3 {
        color: var(--gray-dark);
        font-size: 16px;
        margin-bottom: 16px;
    }

    .card h2 {
        margin-bottom: 20px;
        color: #009879;
        font-size: 22px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        text-align: left;
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
    }

    th {
        background-color: #1b7c3d;
        color: #fff;
    }

    tr:hover {
        background-color: #f1f8f5;
    }

    .note {
        color: #ffc107;
        font-size: 18px;
    }

    .btn-delete {
        background-color: #dc2626;
        color: #fff;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
    }

    .btn-delete:hover {
        background-color: #b91c1c;
    }

    /* Filtres et recherche */
    .filters-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border-left: 4px solid #1b7c3d;
    }

    .filters-section h3 {
        margin-top: 0;
        color: #1b7c3d;
        font-size: 16px;
        margin-bottom: 15px;
    }

    .search-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .search-bar input {
        flex: 1;
        min-width: 200px;
        padding: 10px 12px;
        border: 2px solid #ddd;
        border-radius: 6px;
        font-family: 'Poppins', Arial, sans-serif;
        font-size: 13px;
    }

    .search-bar input:focus {
        outline: none;
        border-color: #1b7c3d;
        box-shadow: 0 0 0 3px rgba(27, 124, 61, 0.1);
    }

    .search-bar button {
        padding: 10px 20px;
        background: #1b7c3d;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-family: 'Poppins', Arial, sans-serif;
        transition: 0.3s;
    }

    .search-bar button:hover {
        background: #159134;
    }

    .reset-filters {
        padding: 8px 16px;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        font-family: 'Poppins', Arial, sans-serif;
    }

    .reset-filters:hover {
        background: #b91c1c;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .filter-group select {
        padding: 8px 10px;
        border: 2px solid #ddd;
        border-radius: 6px;
        font-family: 'Poppins', Arial, sans-serif;
        font-size: 13px;
        cursor: pointer;
    }

    .filter-group select:focus {
        outline: none;
        border-color: #1b7c3d;
    }

    .stats-bar {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .stat-item {
        background: white;
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #1b7c3d;
        border: 1px solid #ddd;
    }

    .pagination-nav {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .pagination-nav a,
    .pagination-nav span {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        color: #1b7c3d;
        font-size: 13px;
        transition: 0.3s;
    }

    .pagination-nav a:hover {
        background: #1b7c3d;
        color: white;
        border-color: #1b7c3d;
    }

    .pagination-nav .current {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        font-weight: 600;
    }

    /* Tables */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table thead {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        position: sticky;
        top: 120px;
        z-index: 10;
    }

    table th {
        color: #fff;
        padding: 14px 16px;
        text-align: left;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--primary-dark);
    }

    table tbody tr {
        border-bottom: 1px solid var(--gray-200);
        transition: var(--transition);
    }

    table tbody tr:hover {
        background: linear-gradient(90deg, var(--primary-light) 0%, #fff 100%);
        box-shadow: inset 4px 0 0 var(--primary);
    }

    table td {
        padding: 12px 16px;
        font-size: 13px;
        color: var(--gray-600);
    }

    table td.note {
        font-size: 14px;
        color: #ffc107;
        font-weight: 600;
    }

    .btn-delete {
        background: var(--danger);
        color: #fff;
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        transition: var(--transition);
        display: inline-block;
    }

    .btn-delete:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .sidebar {
            width: 220px;
        }
        header { margin-left: 220px; }
        main { margin-left: 220px; }
    }

    .toggle-sidebar-btn:hover {
        background: var(--primary-dark);
        transform: scale(1.05);
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 70px;
            align-items: center;
        }
        .sidebar h2 { display: none; }
        .sidebar a { width: 50px; padding: 10px 8px; font-size: 11px; }
        .sidebar { width: 70px; }
        header { margin-left: 70px; }
        header.sidebar-hidden { margin-left: 0; }
        main { margin-left: 70px; padding: 20px; }
        main.sidebar-hidden { margin-left: 0; }
        table { font-size: 12px; }
        table th, table td { padding: 10px 8px; }
    }
</style>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <img src="/validationmodule/view/images/logo.png" alt="Logo SmartLancer" class="logo">
        <h2>SmartLancer</h2>

        <a href="#" class="active">Avis & Évaluations</a>
        <a href="/validationmodule/view/profilfreelancer.php">Profil Freelancer</a>
        <a href="#">Utilisateurs</a>
        <a href="#">Projets</a>
        <a href="#">Statistiques</a>
        <a href="#">Déconnexion</a>
    </div>

    <button class="toggle-sidebar-btn" id="toggleBtn" onclick="toggleSidebar()" style="opacity: 1; visibility: visible; pointer-events: auto;">☰</button>

    <header id="header">
        <h1>Gestion des Avis</h1>
        <input type="search" placeholder="Rechercher un avis...">
    </header>

    <!-- Navigation Bar -->
    <nav style="background: linear-gradient(135deg, #075e3a 0%, #0a5338 100%); padding: 16px 20px; margin-bottom: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="dashboard.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: rgba(255,255,255,0.15); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.2);" onmouseover="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(0)'">
            📊 Dashboard
        </a>
        <a href="reponseadmin.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: rgba(255,255,255,0.15); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.2);" onmouseover="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(0)'">
            💬 Réponses
        </a>
        <a href="avisadmin.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: rgba(255,255,255,0.3); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; border: 1px solid rgba(255,255,255,0.4);">
            ⭐ Avis (Actif)
        </a>
    </nav>

    <main>
        <div class="card">
            <h2>📋 Liste des avis</h2>

            <!-- Filtres et Recherche -->
            <div class="filters-section">
                <h3>🔍 Recherche et Filtres</h3>
                
                <form method="GET" class="search-bar">
                    <input type="text" name="avis_search" placeholder="Rechercher par nom, email ou contenu..." 
                           value="<?= htmlspecialchars($avis_search_keyword) ?>">
                    <button type="submit">🔎 Chercher</button>
                    <?php if (!empty($avis_search_keyword)): ?>
                        <a href="avisadmin.php" class="reset-filters">↺ Réinitialiser</a>
                    <?php endif; ?>
                </form>

                <div class="filter-row">
                    <form method="GET" class="filter-group" style="display: flex; flex-direction: row; gap: 10px; align-items: flex-end;">
                        <div style="flex: 1;">
                            <label>Tri</label>
                            <select name="avis_sort_by" onchange="this.form.submit()">
                                <option value="recent" <?= $avis_sort_by === 'recent' ? 'selected' : '' ?>>Plus récent</option>
                                <option value="oldest" <?= $avis_sort_by === 'oldest' ? 'selected' : '' ?>>Plus ancien</option>
                                <option value="highest_rated" <?= $avis_sort_by === 'highest_rated' ? 'selected' : '' ?>>Mieux noté</option>
                                <option value="lowest_rated" <?= $avis_sort_by === 'lowest_rated' ? 'selected' : '' ?>>Moins noté</option>
                            </select>
                            <input type="hidden" name="avis_search" value="<?= htmlspecialchars($avis_search_keyword) ?>">
                        </div>
                        <div style="flex: 1;">
                            <label>Note min</label>
                            <select name="avis_min_note" onchange="this.form.submit()">
                                <?php for ($n = 1; $n <= 5; $n++): ?>
                                    <option value="<?= $n ?>" <?= $avis_min_note === $n ? 'selected' : '' ?>>⭐ <?= $n ?></option>
                                <?php endfor; ?>
                            </select>
                            <input type="hidden" name="avis_search" value="<?= htmlspecialchars($avis_search_keyword) ?>">
                        </div>
                        <div style="flex: 1;">
                            <label>Note max</label>
                            <select name="avis_max_note" onchange="this.form.submit()">
                                <?php for ($n = 1; $n <= 5; $n++): ?>
                                    <option value="<?= $n ?>" <?= $avis_max_note === $n ? 'selected' : '' ?>>⭐ <?= $n ?></option>
                                <?php endfor; ?>
                            </select>
                            <input type="hidden" name="avis_search" value="<?= htmlspecialchars($avis_search_keyword) ?>">
                        </div>
                    </form>
                </div>

                <!-- Statistiques AVIS -->
                <div class="stats-bar">
                    <div class="stat-item">📊 Total: <strong><?php echo ($avis_stats['total_avis'] ?? 0); ?> avis</strong></div>
                    <div class="stat-item">⭐ Moyenne: <strong><?php echo round($avis_stats['avg_note'] ?? 0, 2); ?>/5</strong></div>
                    <div class="stat-item">📄 Affichés: <strong><?php echo count($allAvis); ?>/<?php echo $total_avis; ?></strong></div>
                </div>
            </div>

            <!-- Tableau des avis -->
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Note</th>
                        <th>Likes</th>
                        <th>Dates</th>
                        <th>Supprimer</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($allAvis) > 0): ?>
                        <?php foreach ($allAvis as $avis): ?>
                            <tr>
                                <td><?= htmlspecialchars($avis['nom']) ?></td>
                                <td><?= htmlspecialchars($avis['email']) ?></td>

                                <td class="note">
                                    <?= str_repeat('★', $avis['note']) . str_repeat('☆', 5 - $avis['note']) ?>
                                </td>

                                <td>
                                    ❤️ <?= $avisModel->getLikesCount($avis['id']); ?>
                                </td>

                                <td>
                                    <div style="font-size:12px">Créé: <?= date('d/m/Y H:i', strtotime($avis['created_at'])) ?></div>
                                    <?php
                                        if (isset($avis['updated_at'])) {
                                            $created = strtotime($avis['created_at']);
                                            $updated = strtotime($avis['updated_at']);
                                            if ($updated > $created) {
                                                echo '<div style="font-size:12px;background:#ffc107;color:#000;padding:2px 6px;border-radius:3px;margin-top:2px;display:inline-block"><strong>✎ Modifié:</strong> ' . date('d/m/Y H:i', $updated) . '</div>';
                                            }
                                        }
                                    ?>
                                </td>

                                <td>
                                    <a href="avisadmin.php?delete_id=<?= $avis['id'] ?>" 
                                       class="btn-delete"
                                       onclick="return confirm('Voulez-vous vraiment supprimer cet avis ?')">
                                       Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center;">Aucun avis trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination AVIS -->
            <?php if ($avis_total_pages > 1): ?>
                <div class="pagination-nav">
                    <?php if ($avis_page > 1): ?>
                        <a href="?<?= http_build_query(array_filter(['avis_search' => $avis_search_keyword, 'avis_sort_by' => $avis_sort_by, 'avis_min_note' => $avis_min_note, 'avis_max_note' => $avis_max_note, 'avis_page' => $avis_page - 1])) ?>">« Préc.</a>
                    <?php endif; ?>

                    <?php for ($p = 1; $p <= $avis_total_pages; $p++): ?>
                        <?php if ($p === $avis_page): ?>
                            <span class="current"><?= $p ?></span>
                        <?php else: ?>
                            <a href="?<?= http_build_query(array_filter(['avis_search' => $avis_search_keyword, 'avis_sort_by' => $avis_sort_by, 'avis_min_note' => $avis_min_note, 'avis_max_note' => $avis_max_note, 'avis_page' => $p])) ?>"><?= $p ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($avis_page < $avis_total_pages): ?>
                        <a href="?<?= http_build_query(array_filter(['avis_search' => $avis_search_keyword, 'avis_sort_by' => $avis_sort_by, 'avis_min_note' => $avis_min_note, 'avis_max_note' => $avis_max_note, 'avis_page' => $avis_page + 1])) ?>">Suiv. »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- RÉPONSES -->
        <div class="card" style="margin-top:20px;">
            <h2>📋 Liste des réponses</h2>

            <!-- Filtres REPONSES -->
            <div class="filters-section">
                <h3>Filtrage et recherche des réponses</h3>
                
                <!-- Search Bar -->
                <form method="GET" class="search-bar" style="margin-bottom: 16px;">
                    <input type="text" name="reponse_search" placeholder="Chercher réponse..." value="<?= htmlspecialchars($reponse_search_keyword) ?>">
                    <button type="submit">🔍 Chercher</button>
                    <?php if (!empty($reponse_search_keyword)): ?>
                        <a href="?" style="display: flex; align-items: center; color: white; text-decoration: none; padding: 10px 12px; background: rgba(255,255,255,0.2); border-radius: 6px; border: 2px solid white; cursor: pointer;">Réinitialiser</a>
                    <?php endif; ?>
                </form>

                <!-- Filters -->
                <form method="GET" class="filter-group">
                    <div class="filter-item">
                        <label for="reponse_sort_by">Tri:</label>
                        <select id="reponse_sort_by" name="reponse_sort_by" onchange="this.form.submit();">
                            <option value="recent" <?= $reponse_sort_by === 'recent' ? 'selected' : '' ?>>Plus récentes</option>
                            <option value="oldest" <?= $reponse_sort_by === 'oldest' ? 'selected' : '' ?>>Plus anciennes</option>
                            <option value="recent_modified" <?= $reponse_sort_by === 'recent_modified' ? 'selected' : '' ?>>Dernièrement modifiées</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label class="checkbox-label">
                            <input type="checkbox" name="reponse_visible_only" value="1" <?= $reponse_visible_only ? 'checked' : '' ?> onchange="this.form.submit();">
                            Visible seulement
                        </label>
                    </div>

                    <!-- Hidden inputs to preserve search state -->
                    <input type="hidden" name="reponse_search" value="<?= htmlspecialchars($reponse_search_keyword) ?>">
                </form>

                <!-- Stats Bar REPONSES -->
                <div class="stats-bar">
                    <div class="stat-item">
                        <span class="stat-value"><?= $reponse_stats['total'] ?? 0 ?></span>
                        <span class="stat-label">Total réponses</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= $reponse_stats['visible_count'] ?? 0 ?></span>
                        <span class="stat-label">Visibles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= $reponse_stats['online_count'] ?? 0 ?></span>
                        <span class="stat-label">En ligne</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= count($allResponses) ?></span>
                        <span class="stat-label">Affichées</span>
                    </div>
                </div>
            </div>

            <!-- Tableau des réponses -->
            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">ID</th>
                        <th style="width: 12%;">Auteur</th>
                        <th style="width: 22%;">Réponse</th>
                        <th style="width: 15%;">Email</th>
                        <th style="width: 12%;">Statut</th>
                        <th style="width: 12%;">À l'avis de</th>
                        <th style="width: 19%;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($allResponses) === 0): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 40px;">📭 Aucune réponse trouvée</td></tr>
                    <?php else: ?>
                        <?php foreach ($allResponses as $r): ?>
                            <tr>
                                <td data-label="ID" style="font-weight: 700; color: var(--primary);">
                                    <?= $r['id'] ?>
                                </td>
                                <td data-label="Auteur">
                                    <div style="font-weight: 600; color: var(--primary);">
                                        <?= htmlspecialchars($r['nom']) ?>
                                    </div>
                                    <div style="font-size: 11px; color: #999; margin-top: 2px;">
                                        <?php 
                                            $is_online = isset($r['is_online']) ? intval($r['is_online']) : 0;
                                            echo $is_online ? '🟢 En ligne' : '⚫ Hors ligne';
                                        ?>
                                    </div>
                                </td>
                                <td data-label="Réponse">
                                    <div style="max-height: 60px; overflow: hidden; text-overflow: ellipsis; font-size: 13px; line-height: 1.4;">
                                        <?= htmlspecialchars(substr($r['contenu'], 0, 120)) ?><?= strlen($r['contenu']) > 120 ? '...' : '' ?>
                                    </div>
                                </td>
                                <td data-label="Email" style="font-size: 12px;">
                                    <?= htmlspecialchars($r['email']) ?>
                                </td>
                                <td data-label="Statut" style="text-align: center;">
                                    <div style="display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; background: <?= (isset($r['visible']) && $r['visible']) ? '#d4edda' : '#f8d7da' ?>; color: <?= (isset($r['visible']) && $r['visible']) ? '#155724' : '#721c24' ?>;">
                                        <?= (isset($r['visible']) && $r['visible']) ? '🌐 Public' : '🔒 Privé' ?>
                                    </div>
                                </td>
                                <td data-label="À l'avis de" style="font-size: 12px;">
                                    <?php if (isset($r['avis_auteur'])): ?>
                                        <span style="font-weight: 600;">
                                            <?= htmlspecialchars(substr($r['avis_auteur'], 0, 20)) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Actions">
                                    <div style="display: flex; gap: 6px;">
                                        <a href="avisadmin.php?delete_response_id=<?= $r['id'] ?>" 
                                           class="btn-delete"
                                           style="padding: 6px 10px; font-size: 12px; display: inline-block; text-decoration: none;"
                                           onclick="return confirm('Voulez-vous vraiment supprimer cette réponse ?')">
                                           🗑️ Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination REPONSES -->
            <?php if ($reponse_total_pages > 1): ?>
                <div class="pagination-nav" style="margin-top: 20px;">
                    <?php if ($reponse_page > 1): ?>
                        <a href="?<?= http_build_query(array_filter(['reponse_search' => $reponse_search_keyword, 'reponse_visible_only' => $reponse_visible_only ? '1' : '', 'reponse_sort_by' => $reponse_sort_by, 'reponse_page' => $reponse_page - 1])) ?>">« Préc.</a>
                    <?php endif; ?>

                    <?php for ($p = 1; $p <= $reponse_total_pages; $p++): ?>
                        <?php if ($p === $reponse_page): ?>
                            <span class="current"><?= $p ?></span>
                        <?php else: ?>
                            <a href="?<?= http_build_query(array_filter(['reponse_search' => $reponse_search_keyword, 'reponse_visible_only' => $reponse_visible_only ? '1' : '', 'reponse_sort_by' => $reponse_sort_by, 'reponse_page' => $p])) ?>"><?= $p ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($reponse_page < $reponse_total_pages): ?>
                        <a href="?<?= http_build_query(array_filter(['reponse_search' => $reponse_search_keyword, 'reponse_visible_only' => $reponse_visible_only ? '1' : '', 'reponse_sort_by' => $reponse_sort_by, 'reponse_page' => $reponse_page + 1])) ?>">Suiv. »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const header = document.getElementById('header');
            const main = document.querySelector('main');
            const toggleBtn = document.getElementById('toggleBtn');

            sidebar.classList.toggle('hidden');
            header.classList.toggle('sidebar-hidden');
            main.classList.toggle('sidebar-hidden');

            // Changer l'icône du bouton
            if (sidebar.classList.contains('hidden')) {
                toggleBtn.textContent = '→'; // Flèche droite quand sidebar cachée
            } else {
                toggleBtn.textContent = '☰'; // Hamburger quand sidebar visible
            }

            // Sauvegarder l'état dans localStorage
            const isHidden = sidebar.classList.contains('hidden');
            localStorage.setItem('sidebarHidden', isHidden);
        }

        // Initialiser au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const header = document.getElementById('header');
            const main = document.querySelector('main');
            const toggleBtn = document.getElementById('toggleBtn');
            
            const sidebarHidden = localStorage.getItem('sidebarHidden') === 'true';
            if (sidebarHidden) {
                sidebar.classList.add('hidden');
                header.classList.add('sidebar-hidden');
                main.classList.add('sidebar-hidden');
                toggleBtn.textContent = '→';
            } else {
                toggleBtn.textContent = '☰';
            }
        });
    </script>

</body>
</html>
