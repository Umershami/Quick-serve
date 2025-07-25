<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../db.php';

$sql = "SELECT * FROM workers WHERE deleted = 1 ORDER BY id DESC";
$result = $conn->query($sql);
$workers = [];

while ($row = $result->fetch_assoc()) {
    $workers[] = $row;
}

echo json_encode(['status' => 'success', 'workers' => $workers]);
?>
