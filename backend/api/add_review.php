<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$pid = (int)($_POST['product_id'] ?? 1);
$author = $_POST['author'] ?? 'Anonymous';
$comment = $_POST['comment'] ?? '';

$c_author = addslashes($author);
$c_comment = addslashes($comment);

$sql = "INSERT INTO reviews (product_id, author, comment) VALUES ($pid, '$c_author', '$c_comment')";
$res = mysqli_query($conn, $sql);

if (!$res) {
    $sql = "INSERT INTO reviews (product_id, user_name, comment) VALUES ($pid, '$c_author', '$c_comment')";
    $res = mysqli_query($conn, $sql);
}

echo json_encode([
    "success" => (bool)$res,
    "error" => mysqli_error($conn)
]);
