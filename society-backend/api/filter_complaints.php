<?php
// ✅ CORS
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include '../db.php';

// ✅ Filters
$status = $_GET['status'] ?? '';
$category = $_GET['category'] ?? '';
$worker = $_GET['worker'] ?? '';
$date = $_GET['date'] ?? '';

// ✅ Build SQL
$sql = "SELECT c.*, r.name AS resident_name, w.name AS worker_name, w.category 
        FROM complaints c
        JOIN residents r ON c.resident_id = r.id
        JOIN workers w ON c.worker_id = w.id
        WHERE c.deleted_by_admin = 0 AND c.deleted_by_resident = 0";

$params = [];
$types = '';

if ($status !== '') {
    $sql .= " AND c.admin_status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($category !== '') {
    $sql .= " AND w.category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($worker !== '') {
    $sql .= " AND w.name LIKE ?";
    $params[] = '%' . $worker . '%';
    $types .= 's';
}

if ($date !== '') {
    $sql .= " AND DATE(c.created_at) = ?";
    $params[] = $date;
    $types .= 's';
}

$sql .= " ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$complaints = [];
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

echo json_encode(['status' => 'success', 'complaints' => $complaints]);
?>
