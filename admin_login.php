<?php
header('Content-Type: application/json');
session_start();

// Admin credentials (in production, use a database)
$validAdmins = [
    'admin' => 'admin123',
    'reception' => 'reception123'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminId = $_POST['adminId'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($validAdmins[$adminId]) && $validAdmins[$adminId] === $password) {
        // Create session
        $_SESSION['admin_id'] = $adminId;
        $_SESSION['login_time'] = time();
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid admin ID or password'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
