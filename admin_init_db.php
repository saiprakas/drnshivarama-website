<?php
// Initialize SQLite database for appointments
$dbPath = __DIR__ . '/appointments.db';

try {
    // Create or open database
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create appointments table if it doesn't exist
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

    // Index for faster queries
    $db->exec("CREATE INDEX IF NOT EXISTS idx_date ON appointments(appointment_date)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_status ON appointments(status)");

    echo "Database initialized successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
