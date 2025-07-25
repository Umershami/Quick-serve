<?php
include 'db.php';

echo json_encode([
    'status' => 'success',
    'message' => 'Database connected successfully'
]);
