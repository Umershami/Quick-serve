<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');

include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password required']);
    exit;
}

$stmt = $conn->prepare("SELECT id, email, password, is_approved FROM workers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No account found']);
    exit;
}

$worker = $res->fetch_assoc();

if ((int)$worker['is_approved'] !== 1) {
    echo json_encode(['status' => 'error', 'message' => 'Account pending approval']);
    exit;
}

if ($password !== $worker['password']) {
    echo json_encode(['status' => 'error', 'message' => 'Wrong password']);
    exit;
}

// ✅ Set both ID and EMAIL in session
$_SESSION['worker_id'] = $worker['id'];
$_SESSION['worker_email'] = $worker['email'];

echo json_encode(['status' => 'success', 'message' => 'Login successful']);
