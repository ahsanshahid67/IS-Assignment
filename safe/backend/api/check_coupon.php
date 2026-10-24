<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$code = trim($_GET['coupon_code'] ?? '');

// Safe: Prepared Statement prevents Intermediate SQL Injection
$stmt = mysqli_prepare($conn, "SELECT id, discount FROM coupons WHERE code = ?");
mysqli_stmt_bind_param($stmt, "s", $code);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($res && mysqli_num_rows($res) > 0) {
    echo json_encode([
        "status" => "true",
        "message" => "Valid Coupon Applied!"
    ]);
} else {
    // Safe: Never leak internal SQL errors or syntax details
    echo json_encode([
        "status" => "false",
        "message" => "Invalid Coupon Code."
    ]);
}

mysqli_stmt_close($stmt);
