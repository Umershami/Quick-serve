<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../db.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid complaint ID']);
  exit;
}

$sql = "UPDATE complaints SET deleted_by_resident = 1 WHERE id = $id";
if ($conn->query($sql)) {
  echo json_encode(['status' => 'success', 'message' => 'Deleted from your panel.']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Delete failed.']);
}
