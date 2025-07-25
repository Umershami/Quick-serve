<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include '../db.php';

$id = intval($_GET['id'] ?? 0);

if (!$id) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
  exit;
}

$sql = "UPDATE complaints SET status = 'read' WHERE id = $id";

if ($conn->query($sql)) {
  echo json_encode(['status' => 'success', 'message' => 'Marked as read.']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to update status.']);
}
?>
