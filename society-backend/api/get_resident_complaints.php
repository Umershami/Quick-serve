<?php
session_start();
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

include '../db.php';

if (!isset($_SESSION['resident_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$resident_id = $_SESSION['resident_id'];

$sql = "SELECT c.*, w.name AS worker_name, w.category, w.deleted AS worker_deleted
        FROM complaints c
        JOIN workers w ON c.worker_id = w.id
        WHERE c.resident_id = ? AND c.deleted_by_resident = 0
        ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resident_id);
$stmt->execute();
$result = $stmt->get_result();

$complaints = [];
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

echo json_encode(['status' => 'success', 'complaints' => $complaints]);
?>
