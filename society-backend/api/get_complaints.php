<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../db.php';

$sql = "
  SELECT c.id, c.resident_name, c.message, c.created_at, 
         c.admin_status, c.resident_confirm,
         w.name AS worker_name, w.category
  FROM complaints c
  JOIN workers w ON c.worker_id = w.id
  WHERE c.deleted_by_admin = 0
  ORDER BY c.created_at DESC
";

$result = $conn->query($sql);

$complaints = [];
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

echo json_encode(['status' => 'success', 'complaints' => $complaints]);
