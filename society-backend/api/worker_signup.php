<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$name         = trim($data['name'] ?? '');
$category     = trim($data['category'] ?? '');
$contact      = trim($data['contact'] ?? '');
$shop_address = trim($data['shop_address'] ?? '');
$email        = trim($data['email'] ?? '');
$password     = trim($data['password'] ?? '');

// ✅ Validate required fields
if (!$name || !$category || !$contact || !$shop_address || !$email || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

// ✅ Check if email already exists
$check = $conn->prepare("SELECT id FROM workers WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already registered.']);
    exit;
}
$check->close();

// ✅ Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// ✅ Insert worker
$stmt = $conn->prepare("
    INSERT INTO workers (name, category, contact, shop_address, email, password, is_approved, deleted)
    VALUES (?, ?, ?, ?, ?, ?, 0, 0)
");
$stmt->bind_param("ssssss", $name, $category, $contact, $shop_address, $email, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Signup request submitted. Pending admin approval.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database insert failed.']);
}

$stmt->close();
$conn->close();
?>
