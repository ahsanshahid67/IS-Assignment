<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

$u = trim($_POST['username'] ?? '');
$p = $_POST['password'] ?? '';

if (empty($u) || empty($p)) {
    echo json_encode(["success" => false, "message" => "Username and password are required"]);
    exit;
}

// Safe: Prepared Statement prevents SQL Injection Authentication Bypass
$stmt = mysqli_prepare($conn, "SELECT id, username, password, email, role FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $u);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

// Verify password (supports both secure hashes and legacy dev passwords)
$authenticated = false;
if ($user) {
    if (password_verify($p, $user['password']) || $p === $user['password']) {
        $authenticated = true;
    }
}

if ($authenticated) {
    // Regenerate session ID to prevent Session Fixation
    session_regenerate_id(true);

    // Refresh CSRF token for new session
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    unset($user['password']); // Never store password in session
    $_SESSION['user'] = $user;

    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "csrf_token" => $_SESSION['csrf_token'],
        "user" => $_SESSION['user']
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid credentials",
        "user" => null
    ]);
}
