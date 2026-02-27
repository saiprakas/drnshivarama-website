<?php
header('Content-Type: application/json');
session_start();

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$dbPath = __DIR__ . '/appointments.db';

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    // Get today's date
    $today = date('Y-m-d');

    if ($action === 'getAppointments') {
        $filter = $_GET['filter'] ?? 'today';
        $sortBy = $_GET['sortBy'] ?? 'date';
        $status = $_GET['status'] ?? 'all';

        $query = "SELECT * FROM appointments WHERE 1=1";
        $params = [];

        // Filter by date range
        if ($filter === 'today') {
            $query .= " AND DATE(appointment_date) = ?";
            $params[] = $today;
        } elseif ($filter === 'week') {
            $query .= " AND DATE(appointment_date) BETWEEN ? AND ?";
            $params[] = $today;
            $params[] = date('Y-m-d', strtotime($today . ' +7 days'));
        } elseif ($filter === 'month') {
            $query .= " AND strftime('%Y-%m', appointment_date) = ?";
            $params[] = date('Y-m');
        } elseif ($filter === 'all') {
            // No additional filter
        }

        // Filter by status
        if ($status !== 'all') {
            $query .= " AND status = ?";
            $params[] = $status;
        }

        // Sort
        if ($sortBy === 'date') {
            $query .= " ORDER BY appointment_date ASC, appointment_time ASC";
        } elseif ($sortBy === 'name') {
            $query .= " ORDER BY name ASC";
        } elseif ($sortBy === 'status') {
            $query .= " ORDER BY status ASC, appointment_date ASC";
        } elseif ($sortBy === 'latest') {
            $query .= " ORDER BY created_at DESC";
        } elseif ($sortBy === 'doctor') {
            $query .= " ORDER BY doctor_requested ASC, appointment_date ASC";
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format the dates for display
        foreach ($appointments as &$apt) {
            $apt['appointment_date_display'] = date('d M, Y', strtotime($apt['appointment_date']));
            $apt['appointment_time_display'] = date('h:i A', strtotime($apt['appointment_time']));
            // also include when the appointment was created (booking time)
            if (!empty($apt['created_at'])) {
                $apt['created_at_display'] = date('d M, Y h:i A', strtotime($apt['created_at']));
            } else {
                $apt['created_at_display'] = '';
            }
            // client local timestamp if provided
            if (!empty($apt['client_timestamp'])) {
                $apt['client_timestamp_raw'] = $apt['client_timestamp'];
                // convert to server timezone display for now
                $apt['client_timestamp_display'] = date('d M, Y h:i A', strtotime($apt['client_timestamp']));
            } else {
                $apt['client_timestamp_display'] = '';
            }
        }

        echo json_encode([
            'success' => true,
            'data' => $appointments,
            'count' => count($appointments)
        ]);

    } elseif ($action === 'updateStatus') {
        $appointmentId = $_POST['appointmentId'] ?? '';
        $newStatus = $_POST['status'] ?? '';

        if (!in_array($newStatus, ['Pending', 'Accepted', 'Declined'])) {
            throw new Exception('Invalid status');
        }

        $stmt = $db->prepare("UPDATE appointments SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $result = $stmt->execute([$newStatus, $appointmentId]);

        echo json_encode([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);

    } elseif ($action === 'deleteAppointment') {
        $appointmentId = $_POST['appointmentId'] ?? '';

        $stmt = $db->prepare("DELETE FROM appointments WHERE id = ?");
        $result = $stmt->execute([$appointmentId]);

        echo json_encode([
            'success' => true,
            'message' => 'Appointment deleted successfully'
        ]);

    } elseif ($action === 'getStats') {
        $today = date('Y-m-d');

        $stats = [
            // appointments scheduled for today
            'total_today' => $db->query("SELECT COUNT(*) as count FROM appointments WHERE DATE(appointment_date) = '$today'")->fetch(PDO::FETCH_ASSOC)['count'],
            'accepted_today' => $db->query("SELECT COUNT(*) as count FROM appointments WHERE DATE(appointment_date) = '$today' AND status = 'Accepted'")->fetch(PDO::FETCH_ASSOC)['count'],
            'pending_today' => $db->query("SELECT COUNT(*) as count FROM appointments WHERE DATE(appointment_date) = '$today' AND status = 'Pending'")->fetch(PDO::FETCH_ASSOC)['count'],
            'declined_today' => $db->query("SELECT COUNT(*) as count FROM appointments WHERE DATE(appointment_date) = '$today' AND status = 'Declined'")->fetch(PDO::FETCH_ASSOC)['count'],
            // appointments booked (created) today regardless of appointment date
            'booked_today' => $db->query("SELECT COUNT(*) as count FROM appointments WHERE DATE(created_at) = '$today'")->fetch(PDO::FETCH_ASSOC)['count'],
            // overall totals ignoring date filter
            'accepted_all' => $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'Accepted'")->fetch(PDO::FETCH_ASSOC)['count'],
            'pending_all' => $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'Pending'")->fetch(PDO::FETCH_ASSOC)['count'],
            'declined_all' => $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'Declined'")->fetch(PDO::FETCH_ASSOC)['count'],
        ];

        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);

    } else {
        throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
