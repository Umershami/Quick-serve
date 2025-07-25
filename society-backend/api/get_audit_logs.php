<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');

include '../db.php';

$complaint_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$complaint_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing complaint ID']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM complaint_status_logs WHERE complaint_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode(['status' => 'success', 'logs' => $logs]);
?>
