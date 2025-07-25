<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");

include '../db.php';

if (!isset($_SESSION['worker_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$worker_id = $_SESSION['worker_id'];

$stmt = $conn->prepare("SELECT email FROM workers WHERE id = ?");
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['status' => 'success', 'worker_id' => $worker_id, 'email' => $row['email']]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Worker not found']);
}
?>
