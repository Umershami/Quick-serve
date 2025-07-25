<?php
$host = '127.0.0.1';
$port = '3307';
$user = 'phpuser';
$password = '123456';
$dbname = 'societyapp'; // Make sure this database exists

$conn = mysqli_connect($host, $user, $password, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
