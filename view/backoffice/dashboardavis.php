<?php 
session_start();
require_once __DIR__ . '/../../model/database.php';
require_once __DIR__ . '/../../model/avis.php';
require_once __DIR__ . '/../../model/reponse.php';

$pdo = (new Database())->getConnection();
$avisModel = new Avis($pdo);
$reponseModel = new Reponse($pdo);

// Déterminer l'onglet actif
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

// Supprimer un avis
if (isset($_GET['delete_id'])) {
    $avisModel->deleteAvis($_GET['delete_id']);
    header("Location: dashboardavis.php?tab=avis");
    exit;
}

// Supprimer une réponse (admin)
if (isset($_GET['delete_response_id'])) {
    $id = (int)$_GET['delete_response_id'];
    if ($id > 0) {
        $reponseModel->deleteById($id);
    }
    header("Location: dashboardavis.php?tab=reponse");
    exit;
}

// ===== NOTIFICATION SETTINGS =====
// Gérer les paramètres de notification
if (isset($_POST['toggle_notifications'])) {
    $new_status = $_POST['toggle_notifications'] === 'enable' ? '1' : '0';
    $saved = false;
    try {
        // Vérifier si app_settings existe
        $checkTable = $pdo->prepare("SHOW TABLES LIKE 'app_settings'");
        $checkTable->execute();
        
        if ($checkTable->rowCount() > 0) {
            // Vérifier si la ligne existe
            $stmt = $pdo->prepare('SELECT id FROM app_settings WHERE name = "notifications_enabled" LIMIT 1');
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $stmt = $pdo->prepare('UPDATE app_settings SET value = :value WHERE name = "notifications_enabled"');
            } else {
                $stmt = $pdo->prepare('INSERT INTO app_settings (name, value, created_at) VALUES ("notifications_enabled", :value, NOW())');
            }
            $stmt->bindParam(':value', $new_status);
            $saved = $stmt->execute();
        } else {
            // table exists check returned 0 rows: fallback to session
            $_SESSION['notifications_enabled'] = $new_status;
            $saved = true;
        }
    } catch (Exception $e) {
        // Table doesn't exist or other DB error, use session fallback
        $_SESSION['notifications_enabled'] = $new_status;
        $saved = true;
    }

    // If request is AJAX, return JSON so front-end can update without redirect
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjax || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => $saved ? 'ok' : 'error', 'notifications_enabled' => $new_status]);
        exit;
    }

    // Fallback: redirect to dashboard
    header("Location: dashboardavis.php?tab=dashboard&notif=updated");
    exit;
}

// Get app setting helper
function getAppSetting($db, $name, $default = null) {
    try {
        $stmt = $db->prepare('SELECT value FROM app_settings WHERE name = :name LIMIT 1');
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['value'])) return $row['value'];
    } catch (Exception $e) {
        // ignore
    }
    // fallback to session if available
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    if (isset($_SESSION[$name])) return $_SESSION[$name];
    return $default;
}

$notifications_enabled = getAppSetting($pdo, 'notifications_enabled', '1') == '1';
$stats_reponses = $reponseModel->getStatistics();
$stats_avis = $avisModel->getStatistics();

// Statistiques détaillées
$query_responders = "SELECT COUNT(DISTINCT email) as unique_responders FROM reponses";
$stmt = $pdo->prepare($query_responders);
$stmt->execute();
$responders = $stmt->fetch(PDO::FETCH_ASSOC);

// Répartition des notes
$query_note_distribution = "SELECT note, COUNT(*) as count FROM avis GROUP BY note ORDER BY note DESC";
$stmt = $pdo->prepare($query_note_distribution);
$stmt->execute();
$note_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tendance mensuelle des avis
$query_avis_trend = "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(id) as avis_count
FROM avis
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC
LIMIT 12";
$stmt = $pdo->prepare($query_avis_trend);
$stmt->execute();
$avis_trend_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tendance mensuelle des réponses
$query_trend = "SELECT 
    DATE_FORMAT(r.created_at, '%Y-%m') as month,
    COUNT(r.id) as reponses_count
FROM reponses r
GROUP BY DATE_FORMAT(r.created_at, '%Y-%m')
ORDER BY month DESC
LIMIT 12";
$stmt = $pdo->prepare($query_trend);
$stmt->execute();
$trend_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Top répondeurs
$query_top = "SELECT email, COUNT(*) as response_count
FROM reponses
GROUP BY email
ORDER BY response_count DESC
LIMIT 10";
$stmt = $pdo->prepare($query_top);
$stmt->execute();
$top_responders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Clients les plus actifs
$query_top_clients = "SELECT nom, email, COUNT(*) as avis_count FROM avis GROUP BY email ORDER BY avis_count DESC LIMIT 10";
$stmt = $pdo->prepare($query_top_clients);
$stmt->execute();
$top_clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistiques de couverture de réponse
$query_reponse_coverage = "SELECT 
    COUNT(DISTINCT a.id) as total_avis,
    COUNT(DISTINCT ar.avis_id) as avis_with_responses
FROM avis a
LEFT JOIN reponses ar ON a.id = ar.avis_id";
$stmt = $pdo->prepare($query_reponse_coverage);
$stmt->execute();
$coverage = $stmt->fetch(PDO::FETCH_ASSOC);

// Dernières réponses
$query_latest_responses = "SELECT id, avis_id, nom, email, contenu, created_at FROM reponses ORDER BY created_at DESC LIMIT 5";
$stmt = $pdo->prepare($query_latest_responses);
$stmt->execute();
$latest_responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===== DONNÉES GESTION AVIS =====
$avis_sort_by = $_GET['avis_sort_by'] ?? 'recent';
$avis_min_note = isset($_GET['avis_min_note']) ? (int)$_GET['avis_min_note'] : 1;
$avis_max_note = isset($_GET['avis_max_note']) ? (int)$_GET['avis_max_note'] : 5;
$avis_search_keyword = $_GET['avis_search'] ?? '';
$avis_page = isset($_GET['avis_page']) ? max(1, (int)$_GET['avis_page']) : 1;
$avis_limit = 15;
$avis_offset = ($avis_page - 1) * $avis_limit;

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
    <title>Dashboard Avis - SmartLancer</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1E90FF;
            --primary-dark: #0a74d6;
            --primary-light: #e0f0ff;
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
            background: linear-gradient(135deg, #d0e8ff, #f0f9ff);
            color: var(--gray-dark);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            margin-bottom: 30px;
            padding: 25px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(30,144,255,0.2);
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        /* Tabs Navigation */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            border-bottom: 2px solid var(--gray-300);
        }

        .tab-btn {
            padding: 12px 24px;
            border: none;
            background: transparent;
            color: var(--gray-600);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tab-btn:hover {
            color: var(--primary);
        }

        .tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Cards */
        .card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-top: 4px solid var(--primary);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .card h2 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: 700;
        }

        .card h3 {
            color: var(--gray-dark);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.8;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 12px;
            color: #999;
            font-weight: 500;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background-color: var(--primary-light);
        }

        /* Buttons */
        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            font-size: 13px;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
        }

        .btn-secondary {
            background-color: var(--gray-300);
            color: var(--gray-dark);
        }

        .btn-secondary:hover {
            background-color: #c5c5c5;
        }

        /* Forms */
        .filters-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid var(--primary);
        }

        .filters-section h3 {
            margin-top: 0;
            color: var(--primary);
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
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.1);
        }

        .search-bar button {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-family: 'Poppins', Arial, sans-serif;
            transition: 0.3s;
        }

        .search-bar button:hover {
            background: var(--primary-dark);
        }

        .reset-filters {
            padding: 8px 16px;
            background: var(--danger);
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
            transition: all 0.3s ease;
        }

        .filter-group select:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Pagination */
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
            color: var(--primary);
            font-size: 13px;
            transition: 0.3s;
        }

        .pagination-nav a:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination-nav .current {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            font-weight: 600;
        }

        .note {
            color: #ffc107;
            font-size: 18px;
        }

        .notification-card {
            background: linear-gradient(135deg, #fff9e6, #fffbf0);
            border-top: 4px solid #ffc107;
        }

        .notification-card h2 {
            color: #ff9800;
        }

        .history-row td {
            padding: 12px 15px !important;
        }

        .history-row .card {
            background: #ffffff;
            border: 1px solid #eee;
        }

        /* Toast */
        .toast {
            position: fixed;
            right: 24px;
            bottom: 24px;
            background: rgba(0,0,0,0.85);
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.2);
            z-index: 2000;
            font-weight: 600;
            opacity: 0;
            transform: translateY(8px);
            transition: opacity 0.18s ease, transform 0.18s ease;
        }
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 80px;
            height: 100vh;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: 4px 0 12px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            z-index: 1000;
            gap: 20px;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 28px;
            border: none;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .sidebar-item.active {
            background: rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .sidebar-tooltip {
            position: absolute;
            left: 90px;
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 1001;
            font-weight: 600;
        }

        .sidebar-item:hover .sidebar-tooltip {
            opacity: 1;
        }

        .main-content {
            margin-left: 80px;
            width: calc(100% - 80px);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
                flex-direction: row;
                margin-bottom: 20px;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .sidebar-item {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }

            .tabs {
                flex-direction: column;
            }

            .tab-btn {
                width: 100%;
                text-align: left;
                border-bottom: none;
                border-left: 3px solid transparent;
            }

            .tab-btn.active {
                border-left-color: var(--primary);
                border-bottom: none;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .filter-row {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <a href="/validationmodule/view/backoffice/dashboardavis.php" class="sidebar-item" title="Dashboard">
            📊
            <span class="sidebar-tooltip">Dashboard</span>
        </a>
        <a href="/validationmodule/view/profilfreelancer.php" class="sidebar-item" title="Retour au Profil">
            👤
            <span class="sidebar-tooltip">Profil</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
    <div class="container">
        <div class="header">
            <h1>📊 Dashboard Avis & Réponses</h1>
            <p>Gérez et analysez tous les avis et réponses de votre plateforme SmartLancer</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="tabs">
            <button class="tab-btn <?= $tab === 'dashboard' ? 'active' : '' ?>" onclick="switchTab('dashboard')">
                📈 Dashboard
            </button>
            <button class="tab-btn <?= $tab === 'avis' ? 'active' : '' ?>" onclick="switchTab('avis')">
                ⭐ Gestion des Avis
            </button>
            <button class="tab-btn <?= $tab === 'reponse' ? 'active' : '' ?>" onclick="switchTab('reponse')">
                💬 Gestion des Réponses
            </button>
        </div>

        <!-- DASHBOARD TAB -->
        <div id="dashboard" class="tab-content <?= $tab === 'dashboard' ? 'active' : '' ?>">
            
            <!-- Notification Settings -->
            <div class="card notification-card">
                <h2>🔔 Paramètres de Notification</h2>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div>
                        <p style="margin: 0 0 10px 0; font-size: 14px; color: #666;">
                            État actuel: <strong><?= $notifications_enabled ? '✅ Activées' : '❌ Désactivées' ?></strong>
                        </p>
                        <p style="margin: 0; font-size: 13px; color: #999;">
                            Les notifications pour les nouvelles réponses sont actuellement <?= $notifications_enabled ? 'ACTIVÉES' : 'DÉSACTIVÉES' ?>
                        </p>
                    </div>
                    <form method="POST" style="margin-left: auto;">
                        <button type="submit" name="toggle_notifications" value="<?= $notifications_enabled ? 'disable' : 'enable' ?>" 
                                class="btn <?= $notifications_enabled ? 'btn-danger' : 'btn-primary' ?>" style="margin: 0;">
                            <?= $notifications_enabled ? '🔴 Désactiver' : '🟢 Activer' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Statistics -->
            <div class="dashboard-grid">
                <div class="card">
                    <h3>📝 Total Avis</h3>
                    <div class="stat-value"><?= $stats_avis['total_avis'] ?? 0 ?></div>
                    <div class="stat-label">Avis publiés</div>
                </div>

                <div class="card">
                    <h3>💬 Total Réponses</h3>
                    <div class="stat-value"><?= $stats_reponses['total_reponses'] ?? 0 ?></div>
                    <div class="stat-label">Réponses enregistrées</div>
                </div>

                <div class="card">
                    <h3>👥 Répondeurs Uniques</h3>
                    <div class="stat-value"><?= $responders['unique_responders'] ?? 0 ?></div>
                    <div class="stat-label">Personnes ayant répondu</div>
                </div>

                <div class="card">
                    <h3>⭐ Note Moyenne</h3>
                    <div class="stat-value"><?= number_format($stats_avis['average_note'] ?? 0, 1) ?>/5</div>
                    <div class="stat-label">Évaluation moyenne</div>
                </div>

                <div class="card">
                    <h3>📊 Couverture</h3>
                    <div class="stat-value"><?= round(($coverage['avis_with_responses'] / max(1, $coverage['total_avis'])) * 100) ?>%</div>
                    <div class="stat-label">Avis avec réponse</div>
                </div>

                <div class="card">
                    <h3>⚡ Taux de Réponse</h3>
                    <div class="stat-value"><?= $responders['unique_responders'] ?? 0 ?></div>
                    <div class="stat-label">Répondeurs actifs</div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="card">
                <h2>⭐ Répartition des Notes</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px; margin-top: 15px;">
                    <?php 
                    $rating_labels = ['5 ⭐⭐⭐⭐⭐', '4 ⭐⭐⭐⭐', '3 ⭐⭐⭐', '2 ⭐⭐', '1 ⭐'];
                    foreach ($rating_labels as $idx => $label):
                        $note = 5 - $idx;
                        $count = 0;
                        foreach ($note_distribution as $dist) {
                            if ((int)$dist['note'] === $note) {
                                $count = $dist['count'];
                                break;
                            }
                        }
                    ?>
                        <div style="text-align: center; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
                            <div style="font-weight: 700; color: #1E90FF; font-size: 18px;"><?= $count ?></div>
                            <div style="font-size: 12px; color: #666;"><?= $label ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top 10 Responders -->
            <div class="card">
                <h2>🏆 Top 10 Répondeurs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Nombre de Réponses</th>
                            <th>Pourcentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($top_responders)):
                            $total_responses = array_sum(array_column($top_responders, 'response_count'));
                            foreach ($top_responders as $idx => $responder):
                        ?>
                            <tr>
                                <td><strong><?= $idx + 1 ?></strong></td>
                                <td><?= htmlspecialchars($responder['email']) ?></td>
                                <td><?= $responder['response_count'] ?></td>
                                <td>
                                    <div style="background: #e0f0ff; border-radius: 4px; padding: 4px 8px; text-align: center; font-weight: 600; color: #1E90FF;">
                                        <?= round(($responder['response_count'] / $total_responses) * 100, 1) ?>%
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #999;">Aucun répondeur</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Top 10 Clients -->
            <div class="card">
                <h2>👥 Top 10 Clients Actifs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Nombre d'Avis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($top_clients)):
                            foreach ($top_clients as $idx => $client):
                        ?>
                            <tr>
                                <td><strong><?= $idx + 1 ?></strong></td>
                                <td><?= htmlspecialchars($client['nom']) ?></td>
                                <td><?= htmlspecialchars($client['email']) ?></td>
                                <td>
                                    <div style="background: #e0f0ff; border-radius: 4px; padding: 4px 8px; text-align: center; font-weight: 600; color: #1E90FF;">
                                        <?= $client['avis_count'] ?>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #999;">Aucun client</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Latest Responses -->
            <div class="card">
                <h2>🆕 Dernières Réponses</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Contenu</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($latest_responses)):
                            foreach ($latest_responses as $response):
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($response['nom']) ?></td>
                                <td><?= htmlspecialchars($response['email']) ?></td>
                                <td><?= substr(htmlspecialchars($response['contenu']), 0, 60) ?>...</td>
                                <td><?= date('d/m/Y H:i', strtotime($response['created_at'])) ?></td>
                            </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #999;">Aucune réponse</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- GESTION AVIS TAB -->
        <div id="avis" class="tab-content <?= $tab === 'avis' ? 'active' : '' ?>">
            <div class="card">
                <h2>⭐ Gestion des Avis</h2>

                <!-- Filters -->
                <div class="filters-section">
                    <h3>🔍 Recherche et Filtres</h3>

                    <form method="GET" class="search-bar">
                        <input type="hidden" name="tab" value="avis">
                        <input type="text" name="avis_search" placeholder="Rechercher par nom, email ou contenu..." 
                               value="<?= htmlspecialchars($avis_search_keyword) ?>">
                        <button type="submit">Rechercher</button>
                    </form>

                    <?php if (!empty($avis_search_keyword)): ?>
                        <a href="?tab=avis" class="reset-filters">↺ Réinitialiser la recherche</a>
                    <?php endif; ?>

                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Tri</label>
                            <form method="GET" style="margin: 0;">
                                <input type="hidden" name="tab" value="avis">
                                <input type="hidden" name="avis_search" value="<?= htmlspecialchars($avis_search_keyword) ?>">
                                <select name="avis_sort_by" onchange="this.form.submit()">
                                    <option value="recent" <?= $avis_sort_by === 'recent' ? 'selected' : '' ?>>Plus récent</option>
                                    <option value="oldest" <?= $avis_sort_by === 'oldest' ? 'selected' : '' ?>>Plus ancien</option>
                                    <option value="highest_rated" <?= $avis_sort_by === 'highest_rated' ? 'selected' : '' ?>>Mieux noté</option>
                                    <option value="lowest_rated" <?= $avis_sort_by === 'lowest_rated' ? 'selected' : '' ?>>Moins noté</option>
                                </select>
                            </form>
                        </div>

                        <div class="filter-group">
                            <label>Note minimale</label>
                            <form method="GET" style="margin: 0;">
                                <input type="hidden" name="tab" value="avis">
                                <input type="hidden" name="avis_search" value="<?= htmlspecialchars($avis_search_keyword) ?>">
                                <input type="hidden" name="avis_sort_by" value="<?= htmlspecialchars($avis_sort_by) ?>">
                                <select name="avis_min_note" onchange="this.form.submit()">
                                    <?php for ($n = 1; $n <= 5; $n++): ?>
                                        <option value="<?= $n ?>" <?= $avis_min_note === $n ? 'selected' : '' ?>>⭐ <?= $n ?> et plus</option>
                                    <?php endfor; ?>
                                </select>
                            </form>
                        </div>

                        <div class="filter-group">
                            <label>Note maximale</label>
                            <form method="GET" style="margin: 0;">
                                <input type="hidden" name="tab" value="avis">
                                <input type="hidden" name="avis_search" value="<?= htmlspecialchars($avis_search_keyword) ?>">
                                <input type="hidden" name="avis_sort_by" value="<?= htmlspecialchars($avis_sort_by) ?>">
                                <select name="avis_max_note" onchange="this.form.submit()">
                                    <?php for ($n = 1; $n <= 5; $n++): ?>
                                        <option value="<?= $n ?>" <?= $avis_max_note === $n ? 'selected' : '' ?>>⭐ <?= $n ?> et moins</option>
                                    <?php endfor; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <h3 style="margin-top: 20px; color: #1E90FF;">
                    📋 Résultats (<?= $total_avis ?> avis trouvés)
                </h3>

                <table>
                    <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Note</th>
                                    <th>Contenu</th>
                                    <th>Date</th>
                                    <th>Dernière modif</th>
                                    <th>Actions</th>
                                </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allAvis)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #999;">Aucun avis trouvé</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allAvis as $avis): ?>
                                <tr>
                                    <td><?= htmlspecialchars($avis['nom']) ?></td>
                                    <td><?= htmlspecialchars($avis['email']) ?></td>
                                    <td>
                                        <span class="note">
                                            <?php for ($i = 0; $i < $avis['note']; $i++) echo '★'; ?>
                                        </span>
                                    </td>
                                    <td><?= substr(htmlspecialchars($avis['contenu']), 0, 50) ?>...</td>
                                    <td><?= date('d/m/Y H:i', strtotime($avis['created_at'])) ?></td>
                                    <td><?= !empty($avis['updated_at']) ? date('d/m/Y H:i', strtotime($avis['updated_at'])) : '-' ?></td>
                                    <td>
                                        <a href="?tab=avis&delete_id=<?= $avis['id'] ?>" class="btn btn-danger" 
                                           onclick="return confirm('Êtes-vous sûr?')">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($avis_total_pages > 1): ?>
                    <div class="pagination-nav">
                        <?php if ($avis_page > 1): ?>
                            <a href="?tab=avis&<?= http_build_query(array_filter(['avis_search' => $avis_search_keyword, 'avis_sort_by' => $avis_sort_by, 'avis_min_note' => $avis_min_note, 'avis_max_note' => $avis_max_note, 'avis_page' => $avis_page - 1])) ?>">« Précédent</a>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $avis_total_pages; $p++): ?>
                            <?php if ($p === $avis_page): ?>
                                <span class="current"><?= $p ?></span>
                            <?php else: ?>
                                <a href="?tab=avis&<?= http_build_query(array_filter(['avis_search' => $avis_search_keyword, 'avis_sort_by' => $avis_sort_by, 'avis_min_note' => $avis_min_note, 'avis_max_note' => $avis_max_note, 'avis_page' => $p])) ?>"><?= $p ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($avis_page < $avis_total_pages): ?>
                            <a href="?tab=avis&<?= http_build_query(array_filter(['avis_search' => $avis_search_keyword, 'avis_sort_by' => $avis_sort_by, 'avis_min_note' => $avis_min_note, 'avis_max_note' => $avis_max_note, 'avis_page' => $avis_page + 1])) ?>">Suivant »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- GESTION REPONSES TAB -->
        <div id="reponse" class="tab-content <?= $tab === 'reponse' ? 'active' : '' ?>">
            <div class="card">
                <h2>💬 Gestion des Réponses</h2>

                <!-- Filters -->
                <div class="filters-section">
                    <h3>🔍 Recherche et Filtres</h3>

                    <form method="GET" class="search-bar">
                        <input type="hidden" name="tab" value="reponse">
                        <input type="text" name="reponse_search" placeholder="Rechercher par nom, email ou contenu..." 
                               value="<?= htmlspecialchars($reponse_search_keyword) ?>">
                        <button type="submit">Rechercher</button>
                    </form>

                    <?php if (!empty($reponse_search_keyword)): ?>
                        <a href="?tab=reponse" class="reset-filters">↺ Réinitialiser la recherche</a>
                    <?php endif; ?>

                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Tri</label>
                            <form method="GET" style="margin: 0;">
                                <input type="hidden" name="tab" value="reponse">
                                <input type="hidden" name="reponse_search" value="<?= htmlspecialchars($reponse_search_keyword) ?>">
                                <select name="reponse_sort_by" onchange="this.form.submit()">
                                    <option value="recent" <?= $reponse_sort_by === 'recent' ? 'selected' : '' ?>>Plus récent</option>
                                    <option value="oldest" <?= $reponse_sort_by === 'oldest' ? 'selected' : '' ?>>Plus ancien</option>
                                </select>
                            </form>
                        </div>

                        <div class="filter-group">
                            <label>Visibilité</label>
                            <form method="GET" style="margin: 0;">
                                <input type="hidden" name="tab" value="reponse">
                                <input type="hidden" name="reponse_search" value="<?= htmlspecialchars($reponse_search_keyword) ?>">
                                <select name="reponse_visible_only" onchange="this.form.submit()">
                                    <option value="0" <?= !$reponse_visible_only ? 'selected' : '' ?>>Tous</option>
                                    <option value="1" <?= $reponse_visible_only ? 'selected' : '' ?>>Visibles uniquement</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <h3 style="margin-top: 20px; color: #1E90FF;">
                    📋 Résultats (<?= $total_reponses ?> réponses trouvées)
                </h3>

                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Avis ID</th>
                            <th>Contenu</th>
                            <th>Date</th>
                            <th>Historique</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allResponses)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #999;">Aucune réponse trouvée</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allResponses as $response): ?>
                                <?php $history = $reponseModel->getVersionHistory($response['id']); ?>
                                <tr>
                                    <td><?= htmlspecialchars($response['nom']) ?></td>
                                    <td><?= htmlspecialchars($response['email']) ?></td>
                                    <td><strong>#<?= $response['avis_id'] ?></strong></td>
                                    <td><?= substr(htmlspecialchars($response['contenu']), 0, 50) ?>...</td>
                                    <td><?= date('d/m/Y H:i', strtotime($response['created_at'])) ?></td>
                                    <td>
                                        <?php if (!empty($history)): ?>
                                            <button type="button" class="btn btn-secondary" onclick="toggleHistory(<?= $response['id'] ?>)">Voir</button>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?tab=reponse&delete_response_id=<?= $response['id'] ?>" class="btn btn-danger" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse?')">Supprimer</a>
                                    </td>
                                </tr>
                                <?php if (!empty($history)): ?>
                                    <tr id="history-row-<?= $response['id'] ?>" class="history-row" style="display:none; background:#fafafa;">
                                        <td colspan="7">
                                            <div class="card" style="padding:12px;">
                                                <strong>Historique des modifications (réponse #<?= $response['id'] ?>)</strong>
                                                <ul style="margin-top:8px;">
                                                    <?php foreach ($history as $ver): ?>
                                                        <li style="margin-bottom:6px; font-size:13px;">
                                                            <strong>v<?= $ver['version'] ?></strong> - <?= htmlspecialchars($ver['nom'] ?? '') ?> (<?= htmlspecialchars($ver['email'] ?? '') ?>) - <?= htmlspecialchars(substr($ver['contenu'] ?? '', 0, 120)) ?>...
                                                            <br><small style="color:#666;">Modifié le: <?= htmlspecialchars($ver['updated_at'] ?? ($ver['created_at'] ?? '')) ?></small>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($reponse_total_pages > 1): ?>
                    <div class="pagination-nav">
                        <?php if ($reponse_page > 1): ?>
                            <a href="?tab=reponse&<?= http_build_query(array_filter(['reponse_search' => $reponse_search_keyword, 'reponse_sort_by' => $reponse_sort_by, 'reponse_visible_only' => $reponse_visible_only ? '1' : '', 'reponse_page' => $reponse_page - 1])) ?>">« Précédent</a>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $reponse_total_pages; $p++): ?>
                            <?php if ($p === $reponse_page): ?>
                                <span class="current"><?= $p ?></span>
                            <?php else: ?>
                                <a href="?tab=reponse&<?= http_build_query(array_filter(['reponse_search' => $reponse_search_keyword, 'reponse_sort_by' => $reponse_sort_by, 'reponse_visible_only' => $reponse_visible_only ? '1' : '', 'reponse_page' => $p])) ?>"><?= $p ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($reponse_page < $reponse_total_pages): ?>
                            <a href="?tab=reponse&<?= http_build_query(array_filter(['reponse_search' => $reponse_search_keyword, 'reponse_sort_by' => $reponse_sort_by, 'reponse_visible_only' => $reponse_visible_only ? '1' : '', 'reponse_page' => $reponse_page + 1])) ?>">Suivant »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            const allTabs = document.querySelectorAll('.tab-content');
            allTabs.forEach(tab => tab.classList.remove('active'));

            // Remove active class from all buttons
            const allBtns = document.querySelectorAll('.tab-btn');
            allBtns.forEach(btn => btn.classList.remove('active'));

            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');

            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        }

        // Handle page load
        document.addEventListener('DOMContentLoaded', function() {
            const tab = new URL(window.location).searchParams.get('tab') || 'dashboard';
            const btn = document.querySelector(`[onclick="switchTab('${tab}')"]`);
            if (btn) {
                btn.classList.add('active');
            }
        });

        function toggleHistory(id) {
            const row = document.getElementById('history-row-' + id);
            if (!row) return;
            row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
            // scroll into view when opening
            if (row.style.display !== 'none') {
                row.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        }
    </script>
    <div id="notif-toast" class="toast" role="status" aria-live="polite" style="display:none"></div>
    <script>
        // AJAX toggle for notification form
        document.addEventListener('DOMContentLoaded', function() {
            const notifForm = document.querySelector('.notification-card form');
            const toast = document.getElementById('notif-toast');
            if (!notifForm) return;

            notifForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = notifForm.querySelector('button[name="toggle_notifications"]');
                const formData = new FormData(notifForm);

                // disable button while processing
                btn.disabled = true;
                const originalText = btn.innerHTML;
                btn.innerHTML = '⏳...';

                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                }).then(res => res.json()).then(data => {
                    btn.disabled = false;
                    // update UI when successful
                    if (data && data.status === 'ok') {
                        const enabled = String(data.notifications_enabled) === '1';
                        // update status text
                        const card = document.querySelector('.notification-card');
                        if (card) {
                            const pState = card.querySelector('p strong');
                            if (pState) pState.textContent = enabled ? '✅ Activées' : '❌ Désactivées';
                            const pSub = card.querySelectorAll('p')[1];
                            if (pSub) pSub.innerHTML = 'Les notifications pour les nouvelles réponses sont actuellement ' + (enabled ? 'ACTIVÉES' : 'DÉSACTIVÉES');
                            // update button appearance and value
                            btn.value = enabled ? 'disable' : 'enable';
                            btn.className = 'btn ' + (enabled ? 'btn-danger' : 'btn-primary');
                            btn.innerHTML = enabled ? '🔴 Désactiver' : '🟢 Activer';
                        }
                        // show toast
                        if (toast) {
                            toast.textContent = enabled ? 'Notifications activées' : 'Notifications désactivées';
                            toast.style.display = 'block';
                            setTimeout(() => toast.classList.add('show'), 10);
                            setTimeout(() => { toast.classList.remove('show'); setTimeout(()=> toast.style.display='none',180); }, 2200);
                        }
                    } else {
                        // error
                        if (toast) {
                            toast.textContent = 'Erreur lors de la mise à jour';
                            toast.style.display = 'block';
                            setTimeout(() => toast.classList.add('show'), 10);
                            setTimeout(() => { toast.classList.remove('show'); setTimeout(()=> toast.style.display='none',180); }, 2200);
                        }
                        btn.innerHTML = originalText;
                    }
                }).catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    if (toast) {
                        toast.textContent = 'Erreur réseau';
                        toast.style.display = 'block';
                        setTimeout(() => toast.classList.add('show'), 10);
                        setTimeout(() => { toast.classList.remove('show'); setTimeout(()=> toast.style.display='none',180); }, 2200);
                    }
                });
            });
        });
    </script>
</body>
</html>
