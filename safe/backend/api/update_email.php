<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

// Safe: Strictly enforce POST requests (blocks GET-based CSRF attacks)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. POST required."]);
    exit;
}

// Check if user is authenticated
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Authentication required."]);
    exit;
}

// Safe: Enforce Anti-CSRF Token Validation
$submitted_token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
$session_token = $_SESSION['csrf_token'] ?? '';

if (empty($submitted_token) || empty($session_token) || !hash_equals($session_token, $submitted_token)) {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Security verification failed: Invalid or missing CSRF token."
    ]);
    exit;
}

// Safe: Validate and sanitize email format
$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid email format."]);
    exit;
}

$id = (int)$_SESSION['user']['id'];

// Safe: Prepared Statement prevents SQL injection
$stmt = mysqli_prepare($conn, "UPDATE users SET email = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "si", $email, $id);
$res = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($res) {
    $_SESSION['user']['email'] = $email;
    echo json_encode([
        "success" => true,
        "message" => "Account email address updated securely."
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Unable to update email."]);
}
