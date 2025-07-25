<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include '../db.php';

$sql = "SELECT id, name, email, category, contact, shop_address, deleted 
        FROM workers 
        WHERE is_approved = 1 AND deleted = 0";



$result = $conn->query($sql);

$workers = [];

while ($row = $result->fetch_assoc()) {
    $workers[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'email' => $row['email'], // ✅ included for complaint routing
        'category' => $row['category'],
        'contact' => $row['contact'],
        'shop_address' => $row['shop_address'],
        'deleted' => $row['deleted']
    ];
}

echo json_encode([
    'status' => 'success',
    'workers' => $workers
]);
?>
