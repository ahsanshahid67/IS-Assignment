<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method not allowed"]);
    exit;
}

$pid = (int)($_POST['product_id'] ?? 1);
$author = trim($_POST['author'] ?? 'Anonymous');
$comment = trim($_POST['comment'] ?? '');

if (empty($comment)) {
    echo json_encode(["success" => false, "error" => "Review comment cannot be empty."]);
    exit;
}

// Safe: Sanitize user input before storing to neutralize Stored XSS
$clean_author = htmlspecialchars(strip_tags($author), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$clean_comment = htmlspecialchars(strip_tags($comment), ENT_QUOTES | ENT_HTML5, 'UTF-8');

// Safe: Parameterized Prepared Statement (Prevents SQLi)
$stmt = mysqli_prepare($conn, "INSERT INTO reviews (product_id, author, comment) VALUES (?, ?, ?)");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "iss", $pid, $clean_author, $clean_comment);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    // Fallback for user_name column if author column not present
    $stmt = mysqli_prepare($conn, "INSERT INTO reviews (product_id, user_name, comment) VALUES (?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iss", $pid, $clean_author, $clean_comment);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $success = false;
    }
}

echo json_encode([
    "success" => $success,
    "error" => $success ? null : "Database error occurred."
]);
