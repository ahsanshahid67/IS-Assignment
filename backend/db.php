<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
if ($origin === 'null') {
    header("Access-Control-Allow-Origin: null");
} else {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => false,
        'samesite' => 'Lax'
    ]);
    session_start();
}
mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect('127.0.0.1', 'root', '', 'techvault');

if (!$conn) {
    $conn = mysqli_connect('127.0.0.1', 'root', 'root', 'techvault');
}

if (!$conn) {
    die(json_encode(["error" => "Database connection failed"]));
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS coupons (id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(50) NOT NULL UNIQUE, discount INT NOT NULL)");
mysqli_query($conn, "INSERT IGNORE INTO coupons (id, code, discount) VALUES (1, 'DISC10', 10), (2, 'CYBER20', 20), (3, 'VIP50', 50)");