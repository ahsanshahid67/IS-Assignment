<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$u = $_POST['username'] ?? '';
$p = $_POST['password'] ?? '';

$sql = "SELECT * FROM users WHERE username = '$u' AND password = '$p'";
$res = mysqli_query($conn, $sql);
$row = ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;

if ($row) {
    $_SESSION['user'] = $row;
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "query" => $sql,
        "user" => $_SESSION['user']
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid credentials",
        "query" => $sql,
        "user" => null
    ]);
}
