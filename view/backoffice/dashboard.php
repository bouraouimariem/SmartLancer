<?php
require_once __DIR__ . '/../../model/database.php';
require_once __DIR__ . '/../../model/reponse.php';
require_once __DIR__ . '/../../model/avis.php';

$db = (new Database())->getConnection();
$reponseModel = new Reponse($db);
$avisModel = new Avis($db);

// Get all statistics
$stats_reponses = $reponseModel->getStatistics();
$stats_avis = $avisModel->getStatistics();

// Get total unique responders
$query_responders = "SELECT COUNT(DISTINCT email) as unique_responders FROM reponses";
$stmt = $db->prepare($query_responders);
$stmt->execute();
$responders = $stmt->fetch(PDO::FETCH_ASSOC);

// Get monthly trend data - seulement avec les colonnes qui existent
$query_trend = "SELECT 
    DATE_FORMAT(r.created_at, '%Y-%m') as month,
    COUNT(r.id) as reponses_count
FROM reponses r
GROUP BY DATE_FORMAT(r.created_at, '%Y-%m')
ORDER BY month DESC
LIMIT 12";
$stmt = $db->prepare($query_trend);
$stmt->execute();
$trend_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get top responders - seulement les colonnes de base
$query_top = "SELECT email, COUNT(*) as response_count
FROM reponses
GROUP BY email
ORDER BY response_count DESC
LIMIT 10";
$stmt = $db->prepare($query_top);
$stmt->execute();
$top_responders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard - SmartLancer</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f3f6f8 0%, #e8f0f5 100%);
    color: #333;
    padding: 20px;
    min-height: 100vh;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
}

.header {
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #075e3a 0%, #0a5338 100%);
    color: white;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
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

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.card {
    background: white;
    padding: 24px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border-top: 4px solid #075e3a;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

.card h3 {
    color: #075e3a;
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
    color: #075e3a;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 12px;
    color: #999;
    font-weight: 500;
}

.stat-change {
    font-size: 11px;
    margin-top: 8px;
    padding: 6px 10px;
    background: #e8f5e9;
    border-radius: 4px;
    color: #2e7d32;
    font-weight: 600;
}

.stat-change.negative {
    background: #ffebee;
    color: #c62828;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #075e3a;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #075e3a;
}

.chart-container {
    position: relative;
    height: 300px;
    background: white;
    padding: 24px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
}

.stats-grid-detailed {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.stat-box {
    background: white;
    padding: 16px;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #eee;
    transition: all 0.3s ease;
}

.stat-box:hover {
    background: #f8f9fa;
    border-color: #075e3a;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
}

.stat-box .value {
    font-size: 24px;
    font-weight: 700;
    color: #075e3a;
    margin-bottom: 4px;
}

.stat-box .label {
    font-size: 11px;
    color: #666;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.icon {
    font-size: 20px;
    margin-right: 8px;
}

.table-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.table-container table {
    width: 100%;
    border-collapse: collapse;
}

.table-container th {
    background: #f8f9fa;
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #075e3a;
    border-bottom: 2px solid #eee;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.table-container td {
    padding: 14px 16px;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}

.table-container tr:hover {
    background: #f8f9fa;
}

.badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.badge-danger {
    background: #f8d7da;
    color: #721c24;
}

.badge-info {
    background: #d1ecf1;
    color: #0c5460;
}

.metric-pair {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .header h1 {
        font-size: 24px;
    }
    
    .chart-container {
        height: 250px;
    }
    
    .metric-pair {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>📊 Dashboard SmartLancer</h1>
        <p>Analyse complète des avis et réponses • Mise à jour en temps réel</p>
    </div>

    <!-- KEY METRICS OVERVIEW -->
    <div class="dashboard-grid">
        <div class="card">
            <h3><span class="icon">📋</span>Total Avis</h3>
            <div class="stat-value"><?= $stats_avis['total_avis'] ?? 0 ?></div>
            <div class="stat-label">Avis reçus</div>
            <div class="stat-change">
                Ce mois: <?= $stats_avis['this_month_count'] ?? 0 ?>
            </div>
        </div>

        <div class="card">
            <h3><span class="icon">💬</span>Total Réponses</h3>
            <div class="stat-value"><?= $stats_reponses['total'] ?? 0 ?></div>
            <div class="stat-label">Réponses publiées</div>
            <div class="stat-change">
                Approuvées: <?= $stats_reponses['approved_count'] ?? 0 ?>
            </div>
        </div>

        <div class="card">
            <h3><span class="icon">⭐</span>Note Moyenne</h3>
            <div class="stat-value"><?= number_format($stats_avis['average_note'] ?? 0, 1) ?>/5</div>
            <div class="stat-label">Évaluation moyenne</div>
            <div class="stat-change">
                Min: <?= $stats_avis['min_note'] ?? 0 ?> • Max: <?= $stats_avis['max_note'] ?? 0 ?>
            </div>
        </div>

        <div class="card">
            <h3><span class="icon">👥</span>Répondeurs</h3>
            <div class="stat-value"><?= $responders['unique_responders'] ?? 0 ?></div>
            <div class="stat-label">Utilisateurs actifs</div>
            <div class="stat-change">
                Taux de réponse: <?= round(($stats_reponses['total'] / max($stats_avis['total_avis'], 1)) * 100, 1) ?>%
            </div>
        </div>
    </div>

    <!-- DETAILED STATISTICS SECTION -->
    <div class="section-title">📈 Statistiques Détaillées</div>

    <!-- AVIS STATISTICS -->
    <div style="margin-bottom: 40px;">
        <h3 style="color: #075e3a; margin-bottom: 16px; font-size: 16px; font-weight: 600;">Distribution des Notes</h3>
        <div class="stats-grid-detailed">
            <div class="stat-box">
                <div class="value">⭐⭐⭐⭐⭐</div>
                <div class="label"><?= $stats_avis['note_5_count'] ?? 0 ?> avis</div>
            </div>
            <div class="stat-box">
                <div class="value">⭐⭐⭐⭐</div>
                <div class="label"><?= $stats_avis['note_4_count'] ?? 0 ?> avis</div>
            </div>
            <div class="stat-box">
                <div class="value">⭐⭐⭐</div>
                <div class="label"><?= $stats_avis['note_3_count'] ?? 0 ?> avis</div>
            </div>
            <div class="stat-box">
                <div class="value">⭐⭐</div>
                <div class="label"><?= $stats_avis['note_2_count'] ?? 0 ?> avis</div>
            </div>
            <div class="stat-box">
                <div class="value">⭐</div>
                <div class="label"><?= $stats_avis['note_1_count'] ?? 0 ?> avis</div>
            </div>
        </div>
    </div>

    <!-- RESPONSE STATISTICS -->
    <div style="margin-bottom: 40px;">
        <h3 style="color: #075e3a; margin-bottom: 16px; font-size: 16px; font-weight: 600;">Statut des Réponses</h3>
        <div class="metric-pair">
            <div class="stats-grid-detailed" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                <?php if (isset($stats_reponses['pending_count'])): ?>
                <div class="stat-box">
                    <div class="value" style="color: #f57f17;">⏳</div>
                    <div class="label"><?= $stats_reponses['pending_count'] ?? 0 ?></div>
                    <div class="label">En attente</div>
                </div>
                <?php endif; ?>
                
                <?php if (isset($stats_reponses['approved_count'])): ?>
                <div class="stat-box">
                    <div class="value" style="color: #2e7d32;">✅</div>
                    <div class="label"><?= $stats_reponses['approved_count'] ?? 0 ?></div>
                    <div class="label">Approuvées</div>
                </div>
                <?php endif; ?>
                
                <?php if (isset($stats_reponses['rejected_count'])): ?>
                <div class="stat-box">
                    <div class="value" style="color: #c62828;">❌</div>
                    <div class="label"><?= $stats_reponses['rejected_count'] ?? 0 ?></div>
                    <div class="label">Rejetées</div>
                </div>
                <?php endif; ?>
            </div>

            <div class="stats-grid-detailed" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                <?php if (isset($stats_reponses['visible_count'])): ?>
                <div class="stat-box">
                    <div class="value" style="color: #1565c0;">🌐</div>
                    <div class="label"><?= $stats_reponses['visible_count'] ?? 0 ?></div>
                    <div class="label">Public</div>
                </div>
                <?php endif; ?>
                
                <?php if (isset($stats_reponses['hidden_count'])): ?>
                <div class="stat-box">
                    <div class="value" style="color: #6a1b9a;">🔒</div>
                    <div class="label"><?= $stats_reponses['hidden_count'] ?? 0 ?></div>
                    <div class="label">Privé</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ROLES & TYPES -->
    <div style="margin-bottom: 40px;">
        <h3 style="color: #075e3a; margin-bottom: 16px; font-size: 16px; font-weight: 600;">Répartition par Rôle & Type</h3>
        <div class="metric-pair">
            <div>
                <p style="font-size: 12px; color: #666; margin-bottom: 12px; font-weight: 600; text-transform: uppercase;">Rôles</p>
                <div class="stats-grid-detailed" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                    <?php if (isset($stats_reponses['freelancer_count'])): ?>
                    <div class="stat-box">
                        <div class="value" style="font-size: 20px;">👤</div>
                        <div class="label"><?= $stats_reponses['freelancer_count'] ?? 0 ?></div>
                        <div class="label">Freelancer</div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($stats_reponses['admin_count'])): ?>
                    <div class="stat-box">
                        <div class="value" style="font-size: 20px;">👨‍💼</div>
                        <div class="label"><?= $stats_reponses['admin_count'] ?? 0 ?></div>
                        <div class="label">Admin</div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($stats_reponses['support_count'])): ?>
                    <div class="stat-box">
                        <div class="value" style="font-size: 20px;">🎯</div>
                        <div class="label"><?= $stats_reponses['support_count'] ?? 0 ?></div>
                        <div class="label">Support</div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($stats_reponses['client_count'])): ?>
                    <div class="stat-box">
                        <div class="value" style="font-size: 20px;">👥</div>
                        <div class="label"><?= $stats_reponses['client_count'] ?? 0 ?></div>
                        <div class="label">Client</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <p style="font-size: 12px; color: #666; margin-bottom: 12px; font-weight: 600; text-transform: uppercase;">Types</p>
                <div class="stats-grid-detailed" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                    <?php if (isset($stats_reponses['type_freelance_count'])): ?>
                    <div class="stat-box">
                        <div class="value" style="font-size: 20px;">💼</div>
                        <div class="label"><?= $stats_reponses['type_freelance_count'] ?? 0 ?></div>
                        <div class="label">Freelance</div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($stats_reponses['type_admin_count'])): ?>
                    <div class="stat-box">
                        <div class="value" style="font-size: 20px;">⚙️</div>
                        <div class="label"><?= $stats_reponses['type_admin_count'] ?? 0 ?></div>
                        <div class="label">Admin</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- CATEGORIES & FEATURES -->
    <div style="margin-bottom: 40px;">
        <h3 style="color: #075e3a; margin-bottom: 16px; font-size: 16px; font-weight: 600;">Catégories & Fonctionnalités</h3>
        <div class="stats-grid-detailed">
            <?php if (isset($stats_reponses['category_thanks_count'])): ?>
            <div class="stat-box">
                <div class="value" style="font-size: 20px;">✨</div>
                <div class="label"><?= $stats_reponses['category_thanks_count'] ?? 0 ?></div>
                <div class="label">Remerciements</div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($stats_reponses['category_justification_count'])): ?>
            <div class="stat-box">
                <div class="value" style="font-size: 20px;">📝</div>
                <div class="label"><?= $stats_reponses['category_justification_count'] ?? 0 ?></div>
                <div class="label">Justifications</div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($stats_reponses['category_improvement_count'])): ?>
            <div class="stat-box">
                <div class="value" style="font-size: 20px;">💡</div>
                <div class="label"><?= $stats_reponses['category_improvement_count'] ?? 0 ?></div>
                <div class="label">Améliorations</div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($stats_reponses['categorized_count'])): ?>
            <div class="stat-box">
                <div class="value" style="font-size: 20px;">🏷️</div>
                <div class="label"><?= $stats_reponses['categorized_count'] ?? 0 ?></div>
                <div class="label">Catégorisées</div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($stats_reponses['with_attachment_count'])): ?>
            <div class="stat-box">
                <div class="value" style="font-size: 20px;">📎</div>
                <div class="label"><?= $stats_reponses['with_attachment_count'] ?? 0 ?></div>
                <div class="label">Avec fichier</div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($stats_reponses['notification_enabled_count'])): ?>
            <div class="stat-box">
                <div class="value" style="font-size: 20px;">📧</div>
                <div class="label"><?= $stats_reponses['notification_enabled_count'] ?? 0 ?></div>
                <div class="label">Notifiées</div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TOP RESPONDERS -->
    <div style="margin-bottom: 40px;">
        <h3 style="color: #075e3a; margin-bottom: 16px; font-size: 16px; font-weight: 600;">🏆 Top 10 Répondeurs</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Classement</th>
                        <th>Email</th>
                        <th>Total Réponses</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; ?>
                    <?php foreach ($top_responders as $responder): ?>
                        <tr>
                            <td>
                                <strong>#<?= $rank ?></strong>
                            </td>
                            <td><?= htmlspecialchars($responder['email']) ?></td>
                            <td>
                                <span class="badge badge-info"><?= $responder['response_count'] ?></span>
                            </td>
                        </tr>
                        <?php $rank++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav style="background: linear-gradient(135deg, #075e3a 0%, #0a5338 100%); padding: 16px 20px; margin-top: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="dashboard.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: rgba(255,255,255,0.3); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; border: 1px solid rgba(255,255,255,0.4);">
            📊 Dashboard (Actif)
        </a>
        <a href="reponseadmin.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: rgba(255,255,255,0.15); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.2);" onmouseover="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(0)'">
            💬 Réponses
        </a>
        <a href="avisadmin.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: rgba(255,255,255,0.15); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.2);" onmouseover="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(0)'">
            ⭐ Avis
        </a>
    </nav>
</div>
</body>
</html>
