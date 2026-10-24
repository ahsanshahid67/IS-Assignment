<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cat = trim($_GET['category'] ?? '');

// Single product query using Prepared Statements (Prevents SQLi)
if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT id, name, category, price, description FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $product = ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);

    echo json_encode([
        "product" => $product,
        "sql_error" => null
    ]);
    exit;
}

// Category filter query using Prepared Statements (Prevents UNION-based SQLi)
if ($cat !== '') {
    $stmt = mysqli_prepare($conn, "SELECT id, name, category, price FROM products WHERE category = ?");
    mysqli_stmt_bind_param($stmt, "s", $cat);
} else {
    $stmt = mysqli_prepare($conn, "SELECT id, name, category, price FROM products");
}

mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$rows = [];

if ($res) {
    while ($r = mysqli_fetch_row($res)) {
        $rows[] = [
            "col1" => $r[0],
            "col2" => $r[1],
            "col3" => $r[2],
            "col4" => $r[3]
        ];
    }
}
mysqli_stmt_close($stmt);

echo json_encode([
    "rows" => $rows,
    "sql_error" => null
]);
