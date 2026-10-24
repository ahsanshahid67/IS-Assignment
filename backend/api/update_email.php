<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$email = $_REQUEST['email'] ?? '';
$id = $_SESSION['user']['id'];

$sql = "UPDATE users SET email = '$email' WHERE id = $id";
$res = mysqli_query($conn, $sql);

if ($res) {
    $_SESSION['user']['email'] = $email;
    if (!empty($_REQUEST['redirect_back'])) {
        header("Location: ../../frontend/login.html");
        exit;
    }
    echo json_encode(["success" => true, "message" => "Email updated"]);
} else {
    echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
}
