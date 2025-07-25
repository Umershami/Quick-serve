<?php
// CORS Setup
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    exit(0);
}

session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");

include '../db.php';

// ✅ Check resident login
if (!isset($_SESSION['resident_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$resident_id = $_SESSION['resident_id'];

// ✅ Get input
$data = json_decode(file_get_contents("php://input"), true);
$worker_email = trim($data['worker_email'] ?? '');
$message = trim($data['message'] ?? '');

// ✅ Validate fields
if (!$worker_email || !$message) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

// ✅ Get worker ID & confirm email exists
$worker_id = 0;
$wq = $conn->prepare("SELECT id, email FROM workers WHERE email = ?");
$wq->bind_param("s", $worker_email);
$wq->execute();
$wr = $wq->get_result();
if ($wrow = $wr->fetch_assoc()) {
    $worker_id = $wrow['id'];
    $worker_email = $wrow['email']; // ensure clean/valid email
} else {
    echo json_encode(['status' => 'error', 'message' => 'No worker found with this email.']);
    exit;
}

// ✅ Get resident name
$res_check = $conn->prepare("SELECT name FROM residents WHERE id = ?");
$res_check->bind_param("i", $resident_id);
$res_check->execute();
$res_result = $res_check->get_result();
if (!$res_result->num_rows) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid resident ID.']);
    exit;
}
$res_row = $res_result->fetch_assoc();
$resident_name = $res_row['name'];

// ✅ Insert complaint (with email)
$sql = "INSERT INTO complaints (
    resident_id, worker_id, worker_email, resident_name, message, created_at,
    status, admin_status, worker_status, resident_confirm
) VALUES (?, ?, ?, ?, ?, NOW(), 'unread', 'pending', 'pending', 'pending')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $resident_id, $worker_id, $worker_email, $resident_name, $message);

// ✅ Save and respond
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Complaint submitted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save complaint.']);
}
?>
