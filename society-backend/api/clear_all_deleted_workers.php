<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../db.php';

$sql = "DELETE FROM workers WHERE deleted = 1";
if ($conn->query($sql)) {
    echo json_encode(['status' => 'success', 'message' => 'All deleted workers permanently removed']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to clear deleted workers']);
}
?>
