<?php
header('Content-Type: application/json');

$dbPath = __DIR__ . '/appointments.db';

try {
    // open database
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ensure appointments table exists
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
        client_timestamp TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // add client_timestamp column if missing
    $cols = $db->query("PRAGMA table_info(appointments)")->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('client_timestamp', $cols)) {
        $db->exec("ALTER TABLE appointments ADD COLUMN client_timestamp TEXT");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $appointmentDate = $_POST['appointmentDate'] ?? '';
        $appointmentTime = $_POST['appointmentTime'] ?? '';
        $doctorRequested = $_POST['doctorRequested'] ?? '';
        $issue = $_POST['issue'] ?? '';
        $clientTs = $_POST['client_timestamp'] ?? null;

        // convert time to 24h for storage
        if (!empty($appointmentTime)) {
            $converted = date('H:i', strtotime($appointmentTime));
            if ($converted) {
                $appointmentTime = $converted;
            }
        }

        // Validate required fields
        if (empty($name) || empty($phone) || empty($appointmentDate) || empty($appointmentTime)) {
            echo json_encode([
                'success' => false,
                'message' => 'Please fill in all required fields'
            ]);
            exit;
        }

        // Insert appointment
        $stmt = $db->prepare("INSERT INTO appointments (name, email, phone, appointment_date, appointment_time, doctor_requested, issue, client_timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $name,
            $email,
            $phone,
            $appointmentDate,
            $appointmentTime,
            $doctorRequested,
            $issue,
            $clientTs
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
