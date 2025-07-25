<?php
session_start();
header('Access-Control-Allow-Origin: http://localhost:3000'); // exact React origin
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Missing email or password']);
    exit;
}

$sql = "SELECT * FROM residents WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['resident_id'] = $user['id'];
        $_SESSION['resident_name'] = $user['name'];
        echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Wrong password']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No account found']);
}
?>
