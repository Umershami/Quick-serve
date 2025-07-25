<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../db.php';

$sql = "SELECT id, name, email, contact, category, shop_address FROM workers WHERE is_approved = 0 AND deleted = 0";
$result = $conn->query($sql);

$workers = [];

while ($row = $result->fetch_assoc()) {
    $workers[] = $row;
}

echo json_encode([
    'status' => 'success',
    'workers' => $workers
]);
?>
