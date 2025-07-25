<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$category = trim($data['category'] ?? '');
$contact = trim($data['contact'] ?? '');
$shop_address = trim($data['shop_address'] ?? '');

if (!$name || !$email || !$password || !$category || !$contact || !$shop_address) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO workers (name, email, password, category, contact, shop_address, is_approved) VALUES (?, ?, ?, ?, ?, ?, 0)");
$stmt->bind_param("ssssss", $name, $email, $password, $category, $contact, $shop_address);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Signup request submitted. Pending admin approval.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Signup failed. Email might already exist.']);
}

$stmt->close();
$conn->close();
?>
