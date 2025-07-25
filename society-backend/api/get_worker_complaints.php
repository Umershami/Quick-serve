<?php
// ✅ CORS Support
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    exit(0);
}

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

session_start();
include '../db.php';

// ✅ Check worker login
if (!isset($_SESSION['worker_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$worker_id = $_SESSION['worker_id'];

// ✅ Fetch worker email from DB
$workerEmail = '';
$stmt = $conn->prepare("SELECT email FROM workers WHERE id = ?");
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    $workerEmail = $row['email'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'Worker not found']);
    exit;
}

// ✅ Now fetch complaints using worker_email
$sql = "SELECT c.*, r.name AS resident_name
        FROM complaints c
        JOIN residents r ON c.resident_id = r.id
        WHERE c.worker_email = ?
          AND c.admin_status = 'resolved'
          AND c.worker_status = 'pending'
          AND c.deleted_by_admin = 0
          AND c.deleted_by_resident = 0
        ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $workerEmail);
$stmt->execute();
$result = $stmt->get_result();

$complaints = [];
while ($row = $result->fetch_assoc()) {
    $row['admin_note'] = $row['admin_note']; // ✅ include admin_note properly
    $complaints[] = $row;
}

// ✅ Also check if this worker is deleted
$check = $conn->prepare("SELECT deleted FROM workers WHERE id = ?");
$check->bind_param("i", $worker_id);
$check->execute();
$delRes = $check->get_result();
$deleted = 0;
if ($r = $delRes->fetch_assoc()) {
    $deleted = $r['deleted'];
}

// ✅ Send response
echo json_encode([
    'status' => 'success',
    'complaints' => $complaints,
    'deleted' => $deleted
]);
?>
