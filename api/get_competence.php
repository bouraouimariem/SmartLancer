<?php
require_once __DIR__ . '/../model/database.php';

$pdo = Database::getConnection();

$q = $_GET['q'] ?? '';

$stmt = $pdo->prepare("SELECT nom FROM competences WHERE nom LIKE :q LIMIT 10");
$stmt->execute(['q' => '%' . $q . '%']);
$data = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($data);
