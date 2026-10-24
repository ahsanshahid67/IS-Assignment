<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$pid = $_GET['product_id'] ?? '';
if ($pid !== '') {
    $sql = "SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON r.product_id = p.id WHERE r.product_id = '$pid' ORDER BY r.id DESC";
} else {
    $sql = "SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON r.product_id = p.id ORDER BY r.id DESC";
}

$res = mysqli_query($conn, $sql);
$reviews = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $reviews[] = $row;
    }
}

echo json_encode([
    "user" => $_SESSION['user'] ?? null,
    "csrf_token" => $_SESSION['csrf_token'] ?? "",
    "reviews" => $reviews
]);
