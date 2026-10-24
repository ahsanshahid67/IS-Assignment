<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? '';
$cat = $_GET['category'] ?? '';

if ($id !== '') {
    $sql = "SELECT id, name, category, price, description FROM products WHERE id = '$id'";
    $res = mysqli_query($conn, $sql);
    $product = ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;
    echo json_encode([
        "product" => $product,
        "sql_error" => mysqli_error($conn)
    ]);
    exit;
}

if ($cat !== '') {
    $sql = "SELECT id, name, category, price FROM products WHERE category = '$cat'";
} else {
    $sql = "SELECT id, name, category, price FROM products";
}

$res = mysqli_query($conn, $sql);
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

echo json_encode([
    "rows" => $rows,
    "sql_error" => mysqli_error($conn)
]);
