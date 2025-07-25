<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');
include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['name'], $data['email'], $data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    exit;
}

$name = $data['name'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_BCRYPT); // Secure hash

$sql = "INSERT INTO residents (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Resident registered']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to register.']);
}
