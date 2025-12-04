<?php
require_once __DIR__ . '/../../model/database.php';
require_once __DIR__ . '/../../model/reponse.php';

$db = (new Database())->getConnection();
$reponseModel = new Reponse($db);

// Filter and search parameters
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$visible_only = isset($_GET['visible_only']) ? ($_GET['visible_only'] === '1') : false;  // Par défaut: afficher TOUTES les réponses
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'recent';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get stats
$stats = $reponseModel->getStatistics();

// Get reponses based on search or filters
if (!empty($search_keyword)) {
    $reponses = $reponseModel->searchReponses($search_keyword, $visible_only, $limit, $offset);
    $total = $reponseModel->countSearchResults($search_keyword, $visible_only);
} else {
    $reponses = $reponseModel->getReponsesByFilters($sort_by, $visible_only, $limit, $offset);
    $total = $reponseModel->countReponsesByFilters($visible_only);
}

$total_pages = ceil($total / $limit);
$displayed = count($reponses);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>BackOffice - Réponses</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;background:#f3f6f8;color:#222;padding:20px}
.container{max-width:1200px;margin:0 auto}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.card{background:#fff;padding:16px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);}
.list .item{border-left:4px solid #075e3a;background:#fff;padding:12px;margin-bottom:12px;border-radius:6px}
.badge{display:inline-block;padding:6px 10px;border-radius:6px;background:#e9f7ef;color:#075e3a;font-weight:700}
.actions{display:flex;gap:8px;margin-top:8px}
.btn{padding:8px 12px;border:none;border-radius:6px;cursor:pointer}
.btn.approve{background:#28a745;color:#fff}
.btn.reject{background:#dc3545;color:#fff}
.btn.delete{background:#6c757d;color:#fff}

/* Filter section styles */
.filters-section {
    background: linear-gradient(135deg, #1b7c3d 0%, #0a5338 100%);
    color: white;
    padding: 24px;
    border-radius: 8px;
    margin-bottom: 24px;
}

.filters-section h3 {
    margin: 0 0 16px 0;
    font-size: 18px;
    font-weight: 600;
}

.search-bar {
    margin-bottom: 16px;
    display: flex;
    gap: 8px;
    align-items: stretch;
}

.search-bar input {
    flex: 1;
    padding: 10px 12px;
    border: none;
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
}

.search-bar button {
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid white;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.search-bar button:hover {
    background: white;
    color: #1b7c3d;
}

.filter-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 16px;
}

.filter-item {
    display: flex;
    flex-direction: column;
}

.filter-item label {
    font-weight: 600;
    margin-bottom: 6px;
    font-size: 13px;
    opacity: 0.95;
}

.filter-item select {
    padding: 8px 10px;
    border: none;
    border-radius: 4px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    cursor: pointer;
}

.filter-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    margin-top: 4px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}

.stats-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
    background: rgba(255, 255, 255, 0.1);
    padding: 16px;
    border-radius: 6px;
    margin-top: 16px;
}

.stat-item {
    text-align: center;
    background: rgba(255, 255, 255, 0.05);
    padding: 12px;
    border-radius: 6px;
    border-left: 3px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: rgba(255, 255, 255, 0.15);
    border-left-color: rgba(255, 255, 255, 0.8);
    transform: translateY(-2px);
}

.stat-item .stat-value {
    font-size: 28px;
    font-weight: 700;
    display: block;
    margin-bottom: 6px;
    color: #fff;
}

.stat-item .stat-label {
    font-size: 11px;
    opacity: 0.95;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pagination-nav {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 24px;
    padding-top: 16px;
    border-top: 1px solid #ddd;
}

.pagination-nav a,
.pagination-nav span {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #075e3a;
    font-weight: 500;
    transition: all 0.2s ease;
}

.pagination-nav a:hover {
    background: #f0f0f0;
    border-color: #075e3a;
}

.pagination-nav span.current {
    background: #075e3a;
    color: white;
    border-color: #075e3a;
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📋 Gestion des réponses</h1>
    </div>

    <!-- Navigation Bar -->
    <nav style="background: linear-gradient(135deg, #075e3a 0%, #0a5338 100%); padding: 16px 20px; margin-bottom: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="dashboard.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: rgba(255,255,255,0.15); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.2);" onmouseover="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(0)'">
            📊 Dashboard
        </a>
        <a href="avisadmin.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: rgba(255,255,255,0.15); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.2);" onmouseover="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(0)'">
            ⭐ Avis
        </a>
        <a href="reponseadmin.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: rgba(255,255,255,0.3); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; border: 1px solid rgba(255,255,255,0.4);">
            💬 Réponses (Actif)
        </a>
    </nav>

    <!-- Filters Section -->
    <div class="filters-section">
        <h3>Recherche et filtrage</h3>
        
        <!-- Search Bar -->
        <form method="GET" class="search-bar" style="margin-bottom: 16px;">
            <input type="text" name="search" placeholder="Rechercher par nom, email ou contenu..." value="<?= htmlspecialchars($search_keyword) ?>">
            <button type="submit">🔍 Chercher</button>
            <?php if (!empty($search_keyword)): ?>
                <a href="?" style="display: flex; align-items: center; color: white; text-decoration: none; padding: 10px 12px; background: rgba(255,255,255,0.2); border-radius: 6px; border: 2px solid white; cursor: pointer;" onclick="return true;">Réinitialiser</a>
            <?php endif; ?>
        </form>

        <!-- Filters -->
        <form method="GET" class="filter-group">
            <div class="filter-item">
                <label for="sort_by">Tri:</label>
                <select id="sort_by" name="sort_by" onchange="this.form.submit();">
                    <option value="recent" <?= $sort_by === 'recent' ? 'selected' : '' ?>>Plus récentes</option>
                    <option value="oldest" <?= $sort_by === 'oldest' ? 'selected' : '' ?>>Plus anciennes</option>
                    <option value="recent_modified" <?= $sort_by === 'recent_modified' ? 'selected' : '' ?>>Dernièrement modifiées</option>
                </select>
            </div>

            <div class="filter-item">
                <label class="checkbox-label">
                    <input type="checkbox" name="visible_only" value="1" <?= $visible_only ? 'checked' : '' ?> onchange="this.form.submit();">
                    Visible seulement
                </label>
            </div>

            <!-- Hidden inputs to preserve search state -->
            <input type="hidden" name="search" value="<?= htmlspecialchars($search_keyword) ?>">
        </form>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-value"><?= $stats['total'] ?? 0 ?></span>
                <span class="stat-label">📊 Total</span>
            </div>
            
            <!-- Visibilité -->
            <div class="stat-item">
                <span class="stat-value"><?= $stats['visible_count'] ?? 0 ?></span>
                <span class="stat-label">🌐 Public</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $stats['hidden_count'] ?? 0 ?></span>
                <span class="stat-label">🔒 Privé</span>
            </div>
            
            <!-- Statut -->
            <div class="stat-item">
                <span class="stat-value"><?= $stats['pending_count'] ?? 0 ?></span>
                <span class="stat-label">⏳ En attente</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $stats['approved_count'] ?? 0 ?></span>
                <span class="stat-label">✅ Approuvées</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $stats['rejected_count'] ?? 0 ?></span>
                <span class="stat-label">❌ Rejetées</span>
            </div>
            
            <!-- Rôles -->
            <div class="stat-item">
                <span class="stat-value"><?= $stats['freelancer_count'] ?? 0 ?></span>
                <span class="stat-label">👤 Freelancer</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $stats['admin_count'] ?? 0 ?></span>
                <span class="stat-label">👨‍💼 Admin</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $stats['support_count'] ?? 0 ?></span>
                <span class="stat-label">🎯 Support</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $stats['client_count'] ?? 0 ?></span>
                <span class="stat-label">👥 Client</span>
            </div>
            
            <!-- Type -->
            <div class="stat-item">
                <span class="stat-value"><?= $stats['type_freelance_count'] ?? 0 ?></span>
                <span class="stat-label">💼 Type Freelance</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $stats['type_admin_count'] ?? 0 ?></span>
                <span class="stat-label">⚙️ Type Admin</span>
            </div>
            
            <!-- Catégories -->
            <div class="stat-item">
                <span class="stat-value"><?= $stats['category_thanks_count'] ?? 0 ?></span>
                <span class="stat-label">✨ Remerciement</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $stats['category_justification_count'] ?? 0 ?></span>
                <span class="stat-label">📝 Justification</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $stats['category_improvement_count'] ?? 0 ?></span>
                <span class="stat-label">💡 Amélioration</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $stats['categorized_count'] ?? 0 ?></span>
                <span class="stat-label">🏷️ Catégorisées</span>
            </div>
            
            <!-- Attachements -->
            <div class="stat-item">
                <span class="stat-value"><?= $stats['with_attachment_count'] ?? 0 ?></span>
                <span class="stat-label">📎 Avec fichier</span>
            </div>
            
            <!-- Notifications -->
            <div class="stat-item">
                <span class="stat-value"><?= $stats['notification_enabled_count'] ?? 0 ?></span>
                <span class="stat-label">📧 Notifiées</span>
            </div>
        </div>
    </div>

    <div class="card list">
        <?php if (count($reponses) === 0): ?>
            <p>Aucune réponse trouvée.</p>
        <?php else: ?>
            <?php foreach ($reponses as $r): ?>
                <div class="item" data-id="<?= $r['id'] ?>">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start">
                        <div>
                            <strong><?= htmlspecialchars($r['nom']) ?></strong>
                            <div style="color:#666;font-size:13px">
                                Email: <?= htmlspecialchars($r['email']) ?> — Créée: <?= isset($r['created_at']) ? date('d/m/Y H:i', strtotime($r['created_at'])) : '' ?>
                                <?php
                                    if (isset($r['updated_at']) && isset($r['created_at'])) {
                                        $created = strtotime($r['created_at']);
                                        $updated = strtotime($r['updated_at']);
                                        if ($updated > $created) {
                                            echo ' <span style="background:#ffc107;color:#000;padding:2px 6px;border-radius:3px;font-weight:bold;font-size:11px">✎ Modifié: ' . date('d/m/Y H:i', $updated) . '</span>';
                                        }
                                    }
                                ?>
                            </div>
                            <div style="margin-top:8px">Réponse: <?= nl2br(htmlspecialchars($r['contenu'])) ?></div>
                            <?php
                                // afficher pièce jointe si disponible
                                if (isset($r['piece_jointe']) && $r['piece_jointe']):
                            ?>
                                <div style="margin-top:8px"><strong>Pièce jointe:</strong> <a href="/validationmodule/<?= htmlspecialchars($r['piece_jointe']) ?>" target="_blank">Ouvrir</a></div>
                            <?php endif; ?>
                            <?php
                                // afficher historique de versions si disponible
                                $history = (function($id){
                                    try { $m = new Reponse((new Database())->getConnection()); return $m->getVersionHistory($id); } catch(\Exception $e) { return []; }
                                })($r['id']);
                                if (!empty($history)):
                            ?>
                                <div style="margin-top:12px">
                                    <strong>Historique des versions:</strong>
                                    <ul>
                                        <?php foreach ($history as $h): ?>
                                            <li>v<?= htmlspecialchars($h['version']) ?> — modifié: <?= htmlspecialchars($h['updated_at'] ?? '') ?> — <?= htmlspecialchars(substr($h['contenu'],0,80)) ?>...</li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <div style="margin-top:8px" class="avis-info"><strong>Avis de:</strong> <?= htmlspecialchars($r['avis_auteur']) ?> — <em><?= nl2br(htmlspecialchars(substr($r['avis_contenu'],0,150))) ?></em></div>
                        </div>
                        <div style="text-align:right">
                            <?php 
                                // Check if is_online field exists and display it
                                $is_online = isset($r['is_online']) ? intval($r['is_online']) : 0;
                            ?>
                            <div style="margin-bottom:8px; padding:8px; border-radius:6px; background:<?= $is_online ? '#e8f5e9' : '#f5f5f5' ?>;  text-align:center;">
                                <div style="font-size:20px; margin-bottom:4px;">
                                    <?php if ($is_online): ?>
                                        🟢
                                    <?php else: ?>
                                        ⚫
                                    <?php endif; ?>
                                </div>
                                <div style="font-weight:bold; font-size:12px; color:<?= $is_online ? '#28a745' : '#666' ?>;">
                                    <?php if ($is_online): ?>
                                        En ligne
                                    <?php else: ?>
                                        Hors ligne
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (isset($r['statut'])): ?>
                                <div style="margin-bottom:6px"><strong>Statut:</strong> <?= htmlspecialchars($r['statut']) ?></div>
                            <?php endif; ?>
                            <?php if (isset($r['role_repondeur'])): ?>
                                <div style="margin-bottom:6px"><strong>Rôle:</strong> <?= htmlspecialchars($r['role_repondeur']) ?></div>
                            <?php endif; ?>
                            <?php if (isset($r['visible'])): ?>
                                <div style="margin-bottom:6px"><strong>Visible:</strong> <?= $r['visible'] ? 'Oui' : 'Non' ?></div>
                            <?php endif; ?>
                            <?php if (isset($r['type'])): ?>
                                <div style="margin-bottom:6px"><strong>Type:</strong> <?= htmlspecialchars($r['type']) ?></div>
                            <?php endif; ?>
                            <div class="badge">ID <?= $r['id'] ?></div>
                            <div class="actions">
                                <button class="btn delete" onclick="deleteReponse(<?= $r['id'] ?>)">Supprimer</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination-nav">
            <?php if ($page > 1): ?>
                <a href="?<?= http_build_query(array_filter(['search' => $search_keyword, 'visible_only' => $visible_only ? '1' : '', 'sort_by' => $sort_by, 'page' => $page - 1])) ?>">« Préc.</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                <?php if ($p === $page): ?>
                    <span class="current"><?= $p ?></span>
                <?php else: ?>
                    <a href="?<?= http_build_query(array_filter(['search' => $search_keyword, 'visible_only' => $visible_only ? '1' : '', 'sort_by' => $sort_by, 'page' => $p])) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?<?= http_build_query(array_filter(['search' => $search_keyword, 'visible_only' => $visible_only ? '1' : '', 'sort_by' => $sort_by, 'page' => $page + 1])) ?>">Suiv. »</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<script src="../reponse-backoffice.js"></script>
</body>
</html>
