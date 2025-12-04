<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/avis.php';
require_once __DIR__ . '/../model/reponse.php';

$db = (new Database())->getConnection();
$avisModel = new Avis($db);
$reponseModel = new Reponse($db);

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$result = ['success' => false, 'message' => 'Action non spécifiée'];

// ========== AVIS ACTIONS ==========
if ($action === 'avis_statistics') {
    $stats = $avisModel->getStatistics();
    echo json_encode(['success' => true, 'data' => $stats]);
    exit;
}

if ($action === 'avis_filter') {
    $sort_by = $_GET['sort_by'] ?? 'recent';
    $min_note = isset($_GET['min_note']) ? (int)$_GET['min_note'] : 1;
    $max_note = isset($_GET['max_note']) ? (int)$_GET['max_note'] : 5;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $avis_list = $avisModel->getAvisByFilters($sort_by, $min_note, $max_note, $limit, $offset);
    $total = $avisModel->countAvisByFilters($min_note, $max_note);

    echo json_encode([
        'success' => true,
        'data' => $avis_list,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ]
    ]);
    exit;
}

if ($action === 'avis_search') {
    $keyword = trim($_GET['keyword'] ?? '');
    if ($keyword === '') {
        $result['message'] = 'Keyword requis.';
        echo json_encode($result);
        exit;
    }

    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $avis_list = $avisModel->searchAvis($keyword, $limit, $offset);
    $total = $avisModel->countSearchResults($keyword);

    echo json_encode([
        'success' => true,
        'data' => $avis_list,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit),
            'keyword' => $keyword
        ]
    ]);
    exit;
}

if ($action === 'avis_by_note') {
    $note = isset($_GET['note']) ? (int)$_GET['note'] : 0;
    if ($note < 1 || $note > 5) {
        $result['message'] = 'Note invalide (1-5).';
        echo json_encode($result);
        exit;
    }

    $avis_list = $avisModel->getAvisByNote($note);
    echo json_encode([
        'success' => true,
        'data' => $avis_list,
        'filters' => ['note' => $note]
    ]);
    exit;
}

// ========== REPONSES ACTIONS ==========
if ($action === 'reponses_statistics') {
    $stats = $reponseModel->getStatistics();
    echo json_encode(['success' => true, 'data' => $stats]);
    exit;
}

if ($action === 'reponses_filter') {
    $sort_by = $_GET['sort_by'] ?? 'recent';
    $visible_only = isset($_GET['visible_only']) ? (bool)$_GET['visible_only'] : true;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $reponses_list = $reponseModel->getReponsesByFilters($sort_by, $visible_only, $limit, $offset);
    $total = $reponseModel->countReponsesByFilters($visible_only);

    echo json_encode([
        'success' => true,
        'data' => $reponses_list,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit),
            'filters' => ['visible_only' => $visible_only, 'sort_by' => $sort_by]
        ]
    ]);
    exit;
}

if ($action === 'reponses_search') {
    $keyword = trim($_GET['keyword'] ?? '');
    if ($keyword === '') {
        $result['message'] = 'Keyword requis.';
        echo json_encode($result);
        exit;
    }

    $visible_only = isset($_GET['visible_only']) ? (bool)$_GET['visible_only'] : true;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $reponses_list = $reponseModel->searchReponses($keyword, $visible_only, $limit, $offset);
    $total = $reponseModel->countSearchResults($keyword, $visible_only);

    echo json_encode([
        'success' => true,
        'data' => $reponses_list,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit),
            'keyword' => $keyword,
            'visible_only' => $visible_only
        ]
    ]);
    exit;
}

if ($action === 'reponses_by_avis') {
    $avis_id = isset($_GET['avis_id']) ? (int)$_GET['avis_id'] : 0;
    if ($avis_id <= 0) {
        $result['message'] = 'ID avis invalide.';
        echo json_encode($result);
        exit;
    }

    $reponses_list = $reponseModel->getReponsesByAvisWithStats($avis_id);
    echo json_encode([
        'success' => true,
        'data' => $reponses_list,
        'filters' => ['avis_id' => $avis_id]
    ]);
    exit;
}

if ($action === 'reponses_by_role') {
    $role = trim($_GET['role'] ?? '');
    if ($role === '' || !in_array($role, ['admin', 'client', 'freelancer'])) {
        $result['message'] = 'Rôle invalide.';
        echo json_encode($result);
        exit;
    }

    $reponses_list = $reponseModel->getReponsesByRole($role);
    echo json_encode([
        'success' => true,
        'data' => $reponses_list,
        'filters' => ['role' => $role]
    ]);
    exit;
}

echo json_encode($result);
?>
