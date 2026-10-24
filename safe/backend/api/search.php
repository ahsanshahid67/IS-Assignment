<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');

// Safe: Sanitize user input to neutralize Reflected XSS
$safe_q = htmlspecialchars($q, ENT_QUOTES | ENT_HTML5, 'UTF-8');

echo json_encode([
    "query" => $safe_q,
    "html" => "Showing search results for: <strong>" . $safe_q . "</strong>"
]);
