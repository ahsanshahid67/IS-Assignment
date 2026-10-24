<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$res = mysqli_query($conn, "DELETE FROM reviews WHERE id > 1");

echo json_encode(["success" => (bool)$res]);
