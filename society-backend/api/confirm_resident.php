<?php
include '../db.php';
header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);
if (!$id) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid complaint ID.']);
  exit;
}

$sql = "UPDATE complaints SET resident_confirm = 'confirmed' WHERE id = $id";
if ($conn->query($sql)) {
  echo json_encode(['status' => 'success', 'message' => 'Marked as confirmed.']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to update.']);
}
?>
