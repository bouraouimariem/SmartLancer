<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/avis.php';
require_once __DIR__ . '/../model/reponse.php';

$db = (new Database())->getConnection();
$avisModel = new Avis($db);
$reponseModel = new Reponse($db);

// Récupérer les statistiques
$avis_stats = $avisModel->getStatistics();
$reponse_stats = $reponseModel->getStatistics();

// Traitement des filtres/recherche
$sort_by = $_GET['sort_by'] ?? 'recent';
$min_note = isset($_GET['min_note']) ? (int)$_GET['min_note'] : 1;
$max_note = isset($_GET['max_note']) ? (int)$_GET['max_note'] : 5;
$search_keyword = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Récupérer les avis filtrés ou recherchés
if (!empty($search_keyword)) {
    $avis_list = $avisModel->searchAvis($search_keyword, $limit, $offset);
    $total_avis = $avisModel->countSearchResults($search_keyword);
} else {
    $avis_list = $avisModel->getAvisByFilters($sort_by, $min_note, $max_note, $limit, $offset);
    $total_avis = $avisModel->countAvisByFilters($min_note, $max_note);
}

$total_pages = ceil($total_avis / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques & Recherche - Avis et Réponses</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f3f6f8 0%, #e9f0f5 100%);
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #0a5338 0%, #075e3a 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .section h2 {
            color: #0a5338;
            font-size: 24px;
            margin-bottom: 25px;
            border-bottom: 3px solid #0a5338;
            padding-bottom: 10px;
        }

        /* Statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #0a5338 0%, #075e3a 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-card .label {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Barre de recherche et filtres */
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .search-bar input {
            flex: 1;
            min-width: 200px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
        }

        .search-bar input:focus {
            outline: none;
            border-color: #0a5338;
            box-shadow: 0 0 0 3px rgba(10, 83, 56, 0.1);
        }

        .search-bar button {
            padding: 12px 25px;
            background: #0a5338;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .search-bar button:hover {
            background: #075e3a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 83, 56, 0.3);
        }

        /* Filtres */
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #0a5338;
            font-size: 13px;
        }

        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #0a5338;
        }

        /* Distribution des notes */
        .notes-distribution {
            margin: 25px 0;
        }

        .note-bar {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            gap: 10px;
        }

        .note-label {
            min-width: 40px;
            font-weight: 600;
            color: #0a5338;
        }

        .note-progress {
            flex: 1;
            height: 20px;
            background: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .note-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #0a5338, #075e3a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: white;
            font-weight: 600;
        }

        .note-count {
            min-width: 60px;
            text-align: right;
            font-weight: 600;
            color: #666;
        }

        /* Avis items */
        .avis-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #0a5338;
            transition: all 0.3s ease;
        }

        .avis-item:hover {
            box-shadow: 0 4px 12px rgba(10, 83, 56, 0.1);
            transform: translateX(5px);
        }

        .avis-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .avis-author {
            font-weight: 600;
            color: #0a5338;
            font-size: 16px;
        }

        .avis-rating {
            color: #ffc107;
            font-size: 16px;
        }

        .avis-meta {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        .avis-content {
            color: #555;
            line-height: 1.6;
            margin: 10px 0;
        }

        .avis-footer {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            font-size: 12px;
            color: #999;
        }

        .avis-likes {
            display: inline-block;
            padding: 4px 8px;
            background: #e9f7ef;
            color: #0a5338;
            border-radius: 4px;
            font-weight: 600;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            text-decoration: none;
            color: #0a5338;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #0a5338;
            color: white;
            border-color: #0a5338;
        }

        .pagination .current {
            background: #0a5338;
            color: white;
            border-color: #0a5338;
            font-weight: 600;
        }

        /* Bouton réinitialiser */
        .reset-filters {
            display: inline-block;
            padding: 8px 16px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .reset-filters:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 24px;
            }

            .section {
                padding: 20px;
            }

            .search-bar {
                flex-direction: column;
            }

            .avis-header {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Statistiques & Recherche</h1>
        <p>Avis et réponses - Filtrage avancé et analyse</p>
    </div>

    <div class="container">
        <!-- STATISTIQUES -->
        <div class="section">
            <h2>📈 Vue d'ensemble</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number"><?= $avis_stats['total_avis'] ?? 0 ?></div>
                    <div class="label">Avis total</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?= round($avis_stats['avg_note'] ?? 0, 1) ?>/5</div>
                    <div class="label">Note moyenne</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?= $reponse_stats['total_reponses'] ?? 0 ?></div>
                    <div class="label">Réponses total</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?= $reponse_stats['online_count'] ?? 0 ?></div>
                    <div class="label">En ligne</div>
                </div>
            </div>

            <!-- Distribution des notes -->
            <div class="notes-distribution">
                <h3 style="margin-bottom: 15px; color: #0a5338;">Distribution des notes</h3>
                <?php for ($note = 5; $note >= 1; $note--): ?>
                    <?php
                    $count = $avis_stats["count_{$note}stars"] ?? 0;
                    $total = $avis_stats['total_avis'] ?? 1;
                    $percentage = ($count / $total) * 100;
                    ?>
                    <div class="note-bar">
                        <div class="note-label">⭐ <?= $note ?></div>
                        <div class="note-progress">
                            <div class="note-bar-fill" style="width: <?= $percentage ?>%;">
                                <?php if ($percentage > 10): echo round($percentage) . '%'; endif; ?>
                            </div>
                        </div>
                        <div class="note-count"><?= $count ?></div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- RECHERCHE ET FILTRES -->
        <div class="section">
            <h2>🔍 Recherche et Filtres</h2>

            <form method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Rechercher par nom, email ou contenu..." 
                       value="<?= htmlspecialchars($search_keyword) ?>">
                <button type="submit">Rechercher</button>
            </form>

            <?php if (!empty($search_keyword)): ?>
                <a href="?" class="reset-filters">↺ Réinitialiser la recherche</a>
            <?php endif; ?>

            <div class="filters">
                <div class="filter-group">
                    <label>Tri</label>
                    <form method="GET" id="sortForm" style="margin: 0;">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_keyword) ?>">
                        <select name="sort_by" onchange="document.getElementById('sortForm').submit()">
                            <option value="recent" <?= $sort_by === 'recent' ? 'selected' : '' ?>>Plus récent</option>
                            <option value="oldest" <?= $sort_by === 'oldest' ? 'selected' : '' ?>>Plus ancien</option>
                            <option value="highest_rated" <?= $sort_by === 'highest_rated' ? 'selected' : '' ?>>Mieux noté</option>
                            <option value="lowest_rated" <?= $sort_by === 'lowest_rated' ? 'selected' : '' ?>>Moins noté</option>
                        </select>
                    </form>
                </div>

                <div class="filter-group">
                    <label>Note minimale</label>
                    <form method="GET" id="minNoteForm" style="margin: 0;">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_keyword) ?>">
                        <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sort_by) ?>">
                        <select name="min_note" onchange="document.getElementById('minNoteForm').submit()">
                            <?php for ($n = 1; $n <= 5; $n++): ?>
                                <option value="<?= $n ?>" <?= $min_note === $n ? 'selected' : '' ?>>⭐ <?= $n ?> et plus</option>
                            <?php endfor; ?>
                        </select>
                    </form>
                </div>

                <div class="filter-group">
                    <label>Note maximale</label>
                    <form method="GET" id="maxNoteForm" style="margin: 0;">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_keyword) ?>">
                        <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sort_by) ?>">
                        <select name="max_note" onchange="document.getElementById('maxNoteForm').submit()">
                            <?php for ($n = 1; $n <= 5; $n++): ?>
                                <option value="<?= $n ?>" <?= $max_note === $n ? 'selected' : '' ?>>⭐ <?= $n ?> et moins</option>
                            <?php endfor; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- RÉSULTATS -->
        <div class="section">
            <h2>
                📋 Résultats 
                <span style="font-size: 16px; color: #666; font-weight: 400;">
                    (<?= $total_avis ?> avis trouvés)
                </span>
            </h2>

            <?php if (empty($avis_list)): ?>
                <p style="text-align: center; color: #999; padding: 40px 0;">Aucun avis trouvé.</p>
            <?php else: ?>
                <?php foreach ($avis_list as $avis): ?>
                    <div class="avis-item">
                        <div class="avis-header">
                            <div class="avis-author">👤 <?= htmlspecialchars($avis['nom']) ?></div>
                            <div class="avis-rating">
                                <?php 
                                    for ($i = 0; $i < $avis['note']; $i++) echo '★';
                                    for ($i = $avis['note']; $i < 5; $i++) echo '☆';
                                ?>
                            </div>
                        </div>
                        <div class="avis-meta">
                            📧 <?= htmlspecialchars($avis['email']) ?> • 📅 <?= date('d/m/Y H:i', strtotime($avis['created_at'])) ?>
                            <?php if ($avis['updated_at'] && $avis['updated_at'] !== $avis['created_at']): ?>
                            • ✎ Modifié: <?= date('d/m/Y H:i', strtotime($avis['updated_at'])) ?>
                            <?php endif; ?>
                        </div>
                        <div class="avis-content">
                            <?= nl2br(htmlspecialchars($avis['contenu'])) ?>
                        </div>
                        <div class="avis-footer">
                            <span class="avis-likes">❤️ <?= $avisModel->getLikesCount($avis['id']) ?> likes</span>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?= http_build_query(array_filter(['search' => $search_keyword, 'sort_by' => $sort_by, 'min_note' => $min_note, 'max_note' => $max_note, 'page' => $page - 1])) ?>">« Précédent</a>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                            <?php if ($p === $page): ?>
                                <span class="current"><?= $p ?></span>
                            <?php else: ?>
                                <a href="?<?= http_build_query(array_filter(['search' => $search_keyword, 'sort_by' => $sort_by, 'min_note' => $min_note, 'max_note' => $max_note, 'page' => $p])) ?>"><?= $p ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?<?= http_build_query(array_filter(['search' => $search_keyword, 'sort_by' => $sort_by, 'min_note' => $min_note, 'max_note' => $max_note, 'page' => $page + 1])) ?>">Suivant »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
