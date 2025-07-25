<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../db.php';

$id = $_GET['id'] ?? '';

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Worker ID required']);
    exit;
}

$sql = "DELETE FROM workers WHERE id = $id AND deleted = 1";
if ($conn->query($sql)) {
    echo json_encode(['status' => 'success', 'message' => 'Worker permanently deleted']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete worker']);
}
?>
