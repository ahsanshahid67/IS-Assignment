<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$code = $_GET['coupon_code'] ?? '';

$sql = "SELECT id, discount FROM coupons WHERE code = '$code'";
$res = mysqli_query($conn, $sql);

if (!$res) {
    echo json_encode([
        "status" => "error",
        "message" => mysqli_error($conn)
    ]);
} elseif (mysqli_num_rows($res) > 0) {
    echo json_encode([
        "status" => "true",
        "message" => "Valid Coupon!"
    ]);
} else {
    echo json_encode([
        "status" => "false",
        "message" => "Invalid Coupon!"
    ]);
}
