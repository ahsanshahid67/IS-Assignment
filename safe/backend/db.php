<?php

$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
if ($origin === 'null') {
    header("Access-Control-Allow-Origin: null");
} else {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token");

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set to true in HTTPS production
        'httponly' => true, // Prevents XSS cookie theft
        'samesite' => 'Strict' // Prevents cross-site cookie transmission (Anti-CSRF)
    ]);
    session_start();
}

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect('127.0.0.1', 'root', '', 'techvault');
if (!$conn) {
    $conn = mysqli_connect('127.0.0.1', 'root', 'root', 'techvault');
}

if (!$conn) {
    http_response_code(500);
    die(json_encode(["error" => "Secure database connection unavailable."]));
}

// Ensure required tables exist
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS coupons (id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(50) NOT NULL UNIQUE, discount INT NOT NULL)");
mysqli_query($conn, "INSERT IGNORE INTO coupons (id, code, discount) VALUES (1, 'DISC10', 10), (2, 'CYBER20', 20), (3, 'VIP50', 50)");
