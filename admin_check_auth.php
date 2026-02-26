<?php
header('Content-Type: application/json');
session_start();

if (isset($_SESSION['admin_id'])) {
    echo json_encode([
        'success' => true,
        'admin_id' => $_SESSION['admin_id']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
}
?>
