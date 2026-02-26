<?php
header('Content-Type: application/json');

$dbPath = __DIR__ . '/appointments.db';

try {
    // Create database if it doesn't exist
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL,
        appointment_date TEXT NOT NULL,
        appointment_time TEXT NOT NULL,
        doctor_requested TEXT NOT NULL,
        issue TEXT NOT NULL,
        status TEXT DEFAULT 'Pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $appointmentDate = $_POST['appointmentDate'] ?? '';
        $appointmentTime = $_POST['appointmentTime'] ?? '';
        $doctorRequested = $_POST['doctorRequested'] ?? '';
        $issue = $_POST['issue'] ?? '';

        // Validate required fields
        if (empty($name) || empty($phone) || empty($appointmentDate) || empty($appointmentTime)) {
            echo json_encode([
                'success' => false,
                'message' => 'Please fill in all required fields'
            ]);
            exit;
        }

        // Insert appointment
        $stmt = $db->prepare("
            INSERT INTO appointments (name, email, phone, appointment_date, appointment_time, doctor_requested, issue)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $result = $stmt->execute([
            $name,
            $email,
            $phone,
            $appointmentDate,
            $appointmentTime,
            $doctorRequested,
            $issue
        ]);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Appointment booked successfully!',
                'appointment_id' => $db->lastInsertId()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to book appointment'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
