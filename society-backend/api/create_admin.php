<?php
include '../db.php';

$email = 'admin@example.com';
$password = password_hash('b17society12341', PASSWORD_DEFAULT); // Use secure hash

$sql = "INSERT INTO admins (email, password) VALUES ('$email', '$password')";
if ($conn->query($sql)) {
    echo "Admin created successfully.";
} else {
    echo "Error: " . $conn->error;
}
?>
