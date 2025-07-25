<?php
// ✅ CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header("Access-Control-Allow-Origin: http://localhost:3000");
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Allow-Headers: Content-Type");
  header("Access-Control-Allow-Methods: POST, OPTIONS");
  exit(0);
}

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

session_start();
include '../db.php';

if (!isset($_SESSION['resident_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
  exit;
}

$resident_id = $_SESSION['resident_id'];
$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);
$reason = trim($data['reason'] ?? '');

if (!$id || $reason === '') {
  echo json_encode(['status' => 'error', 'message' => 'Invalid complaint ID or reason']);
  exit;
}

// ✅ Verify that complaint belongs to resident
$check = $conn->prepare("SELECT id, admin_status FROM complaints WHERE id = ? AND resident_id = ?");
$check->bind_param("ii", $id, $resident_id);
$check->execute();
$result = $check->get_result();

if (!$result->num_rows) {
  echo json_encode(['status' => 'error', 'message' => 'Complaint not found or access denied']);
  exit;
}

$complaint = $result->fetch_assoc();
if ($complaint['admin_status'] !== 'resolved') {
  echo json_encode(['status' => 'error', 'message' => 'You can only reopen resolved complaints']);
  exit;
}

// ✅ Reopen complaint
$update = $conn->prepare("UPDATE complaints SET admin_status = 'pending', worker_status = 'pending' WHERE id = ?");
$update->bind_param("i", $id);
$update->execute();

// ✅ Log to audit trail
$log = $conn->prepare("INSERT INTO complaint_status_logs 
  (complaint_id, action, status_from, status_to, performed_by, note) 
  VALUES (?, 'reopen', 'resolved', 'pending', 'resident', ?)");
$log->bind_param("is", $id, $reason);
$log->execute();

echo json_encode(['status' => 'success', 'message' => 'Complaint reopened successfully.']);
?>
