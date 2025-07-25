<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../db.php';

$id = $_GET['id'] ?? 0;
$id = intval($id);

$sql = "UPDATE workers SET is_approved = 1 WHERE id = $id";
if ($conn->query($sql)) {
    echo json_encode(['status' => 'success', 'message' => 'Worker approved.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to approve.']);
}
?>
