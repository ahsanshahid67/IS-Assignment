<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$pid = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($pid > 0) {
    $stmt = mysqli_prepare($conn, "SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON r.product_id = p.id WHERE r.product_id = ? ORDER BY r.id DESC");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $pid);
    }
} else {
    $stmt = mysqli_prepare($conn, "SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON r.product_id = p.id ORDER BY r.id DESC");
}

$reviews = [];
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $author = $row['author'] ?? $row['user_name'] ?? 'Customer';
            $row['author'] = htmlspecialchars($author, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $row['comment'] = htmlspecialchars($row['comment'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $row['product_name'] = htmlspecialchars($row['product_name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $reviews[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
} else {
    // Direct safe fallback if prepare is unavailable
    $sql = ($pid > 0) 
        ? "SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON r.product_id = p.id WHERE r.product_id = $pid ORDER BY r.id DESC"
        : "SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON r.product_id = p.id ORDER BY r.id DESC";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $author = $row['author'] ?? $row['user_name'] ?? 'Customer';
            $row['author'] = htmlspecialchars($author, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $row['comment'] = htmlspecialchars($row['comment'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $row['product_name'] = htmlspecialchars($row['product_name'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $reviews[] = $row;
        }
    }
}

echo json_encode([
    "user" => $_SESSION['user'] ?? null,
    "csrf_token" => $_SESSION['csrf_token'] ?? "",
    "reviews" => $reviews
]);
