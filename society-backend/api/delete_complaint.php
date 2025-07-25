<?php
// ✅ CORS
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

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid complaint ID']);
    exit;
}

$stmt = $conn->prepare("UPDATE complaints SET deleted_by_admin = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Deleted from admin panel.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Delete failed.']);
}
?>
