<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$q = $_GET['q'] ?? '';

echo json_encode([
    "html" => "Showing search results for: <strong>$q</strong>"
]);
