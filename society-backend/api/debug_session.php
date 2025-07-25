<?php
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 'false');
session_start();
header('Content-Type: application/json');

echo json_encode([
  'worker_id' => $_SESSION['worker_id'] ?? null,
  'session_id' => session_id()
]);
