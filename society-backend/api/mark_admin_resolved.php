<?php
// ✅ Handle CORS preflight for POST
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    exit(0); // End preflight
}

// ✅ Main headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);
$admin_note = trim($data['admin_note'] ?? '');

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid complaint ID']);
    exit;
}

$stmt = $conn->prepare("UPDATE complaints 
    SET admin_status = 'resolved', worker_status = 'pending', admin_note = ? 
    WHERE id = ?");
$stmt->bind_param("si", $admin_note, $id);
$success = $stmt->execute();

if ($success) {
    $log = $conn->prepare("INSERT INTO complaint_status_logs 
        (complaint_id, action, status_from, status_to, performed_by, note) 
        VALUES (?, 'mark_resolved', 'pending', 'resolved', 'admin', ?)");
    $log->bind_param("is", $id, $admin_note);
    $log->execute();

    echo json_encode(['status' => 'success', 'message' => 'Marked as resolved and sent to worker.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}
?>
