<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include '../db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
  echo json_encode(['status' => 'error', 'message' => 'Missing ID']);
  exit;
}

$sql = "UPDATE complaints SET resident_confirm = 'confirmed' WHERE id = $id";
if ($conn->query($sql) === TRUE) {
  echo json_encode(['status' => 'success', 'message' => 'Complaint marked as fixed.']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to update.']);
}
?>
