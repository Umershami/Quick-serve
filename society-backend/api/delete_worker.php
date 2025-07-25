<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid worker ID']);
    exit;
}

$checkWorker = "SELECT * FROM workers WHERE id = $id LIMIT 1";
$result = $conn->query($checkWorker);
if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Worker not found.']);
    exit;
}

$deleteWorker = "UPDATE workers SET deleted = 1 WHERE id = $id";
if ($conn->query($deleteWorker)) {
    $conn->query("UPDATE complaints SET worker_deleted = 1 WHERE worker_id = $id");
    echo json_encode(['status' => 'success', 'message' => 'Worker deleted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete worker.']);
}
?>
