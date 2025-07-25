<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');

include '../db.php';

// ✅ Check if worker is logged in
if (!isset($_SESSION['worker_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$worker_id = $_SESSION['worker_id'];
$id = intval($_GET['id'] ?? 0);

// ✅ Validate complaint ID
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid complaint ID.']);
    exit;
}

// ✅ Update only if this complaint is assigned to the logged-in worker
$sql = "UPDATE complaints 
        SET worker_status = 'done' 
        WHERE id = ? AND worker_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $worker_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Complaint marked as resolved.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update.']);
}
?>
