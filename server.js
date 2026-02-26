const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');
const fs = require('fs');
const sqlite3 = require('sqlite3').verbose();
const session = require('express-session');

const app = express();
const PORT = 8000;
const DB_PATH = path.join(__dirname, 'appointments.db');

// Middleware
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Simple CORS + credentials support for local development
app.use((req, res, next) => {
    const origin = req.headers.origin;
    if (origin) {
        res.header('Access-Control-Allow-Origin', origin);
    }
    res.header('Access-Control-Allow-Credentials', 'true');
    res.header('Access-Control-Allow-Methods', 'GET,POST,OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type');
    if (req.method === 'OPTIONS') return res.sendStatus(200);
    next();
});

// Session middleware
app.use(session({
    secret: 'your_secret_key_change_this',
    resave: false,
    saveUninitialized: true,
    cookie: { 
         secure: false,
         maxAge: 24 * 60 * 60 * 1000, // 24 hours
         sameSite: 'lax',
         path: '/'
    },
    rolling: true
}));

// Initialize SQLite Database
function initDatabase() {
    return new Promise((resolve, reject) => {
        const db = new sqlite3.Database(DB_PATH, (err) => {
            if (err) reject(err);
            
            db.run(`CREATE TABLE IF NOT EXISTS appointments (
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
            )`, (err) => {
                if (err) reject(err);
                
                // Create index
                db.run(`CREATE INDEX IF NOT EXISTS idx_date ON appointments(appointment_date)`, (err) => {
                    if (err) reject(err);
                    db.close();
                    resolve();
                });
            });
        });
    });
}

// Admin Credentials (CHANGE THESE!)
const validAdmins = {
    'admin': 'admin123',
    'reception': 'reception123'
};

// Routes

// 1. Login Route
app.post('/admin_login.php', (req, res) => {
    const { adminId, password } = req.body;
    
    if (validAdmins[adminId] && validAdmins[adminId] === password) {
        req.session.admin_id = adminId;
        res.json({
            success: true,
            message: 'Login successful'
        });
    } else {
        res.json({
            success: false,
            message: 'Invalid admin ID or password'
        });
    }
});

// 2. Check Auth Route
app.get('/admin_check_auth.php', (req, res) => {
    if (req.session.admin_id) {
        res.json({
            success: true,
            admin_id: req.session.admin_id
        });
    } else {
        res.json({
            success: false,
            message: 'Not authenticated'
        });
    }
});

// 3. Logout Route
app.get('/admin_logout.php', (req, res) => {
    req.session.destroy();
    res.json({ success: true });
});

// 4. Save Appointment Route
app.post('/save_appointment.php', (req, res) => {
    if (!req.session.admin_id && !req.body.name) {
        // Allow public appointment booking
    }

    console.log('[save_appointment] incoming body:', req.body);

    const { name, email, phone, appointmentDate, appointmentTime, doctorRequested, issue } = req.body;

    if (!name || !phone || !appointmentDate || !appointmentTime) {
        return res.json({
            success: false,
            message: 'Please fill in all required fields'
        });
    }

    const db = new sqlite3.Database(DB_PATH);
    
    db.run(
        `INSERT INTO appointments (name, email, phone, appointment_date, appointment_time, doctor_requested, issue)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [name, email || '', phone, appointmentDate, appointmentTime, doctorRequested || '', issue || ''],
        function(err) {
            db.close();
            if (err) {
                return res.json({
                    success: false,
                    message: 'Failed to book appointment: ' + err.message
                });
            }
            res.json({
                success: true,
                message: 'Appointment booked successfully!',
                appointment_id: this.lastID
            });
        }
    );
});

// 5. Admin API Route
app.all('/admin_api.php', (req, res) => {
    if (!req.session.admin_id) {
        return res.status(401).json({
            success: false,
            message: 'Unauthorized'
        });
    }

    // Support both GET (query) and POST (body/FormData)
    const action = (req.method === 'GET') ? req.query.action : (req.body && req.body.action) ? req.body.action : req.query.action;
    const db = new sqlite3.Database(DB_PATH);

    if (action === 'getAppointments') {
        const filter = req.method === 'GET' ? (req.query.filter || 'today') : (req.body.filter || 'today');
        const sortBy = req.method === 'GET' ? (req.query.sortBy || 'date') : (req.body.sortBy || 'date');
        const status = req.method === 'GET' ? (req.query.status || 'all') : (req.body.status || 'all');
        const today = new Date().toISOString().split('T')[0];

        let query = 'SELECT * FROM appointments WHERE 1=1';
        let params = [];

        if (filter === 'today') {
            query += " AND DATE(appointment_date) = ?";
            params.push(today);
        } else if (filter === 'week') {
            const weekEnd = new Date(new Date().setDate(new Date().getDate() + 7)).toISOString().split('T')[0];
            query += " AND DATE(appointment_date) BETWEEN ? AND ?";
            params.push(today);
            params.push(weekEnd);
        } else if (filter === 'month') {
            const month = today.substring(0, 7);
            query += " AND substr(appointment_date, 1, 7) = ?";
            params.push(month);
        }

        if (status !== 'all') {
            query += " AND status = ?";
            params.push(status);
        }

        if (sortBy === 'date') {
            query += " ORDER BY appointment_date ASC, appointment_time ASC";
        } else if (sortBy === 'name') {
            query += " ORDER BY name ASC";
        } else if (sortBy === 'status') {
            query += " ORDER BY status ASC, appointment_date ASC";
        } else if (sortBy === 'latest') {
            query += " ORDER BY created_at DESC";
        } else if (sortBy === 'doctor') {
            query += " ORDER BY doctor_requested ASC, appointment_date ASC";
        }

        db.all(query, params, (err, rows) => {
            db.close();
            if (err) {
                return res.json({ success: false, message: err.message });
            }

            const formattedRows = rows.map(apt => ({
                ...apt,
                appointment_date_display: new Date(apt.appointment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }),
                appointment_time_display: new Date('2000-01-01T' + apt.appointment_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })
            }));

            res.json({
                success: true,
                data: formattedRows,
                count: formattedRows.length
            });
        });

    } else if (action === 'updateStatus') {
        const appointmentId = req.method === 'GET' ? req.query.appointmentId : req.body.appointmentId;
        const newStatus = req.method === 'GET' ? req.query.status : req.body.status;

        if (!['Pending', 'Accepted', 'Declined'].includes(newStatus)) {
            db.close();
            return res.json({ success: false, message: 'Invalid status' });
        }

        db.run(
            `UPDATE appointments SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`,
            [newStatus, appointmentId],
            function(err) {
                db.close();
                if (err) {
                    return res.json({ success: false, message: err.message });
                }
                res.json({
                    success: true,
                    message: 'Status updated successfully'
                });
            }
        );

    } else if (action === 'deleteAppointment') {
        const appointmentId = req.method === 'GET' ? req.query.appointmentId : req.body.appointmentId;

        db.run(
            `DELETE FROM appointments WHERE id = ?`,
            [appointmentId],
            function(err) {
                db.close();
                if (err) {
                    return res.json({ success: false, message: err.message });
                }
                res.json({
                    success: true,
                    message: 'Appointment deleted successfully'
                });
            }
        );

    } else if (action === 'getStats') {
        const today = new Date().toISOString().split('T')[0];

        db.all(`
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'Declined' THEN 1 ELSE 0 END) as declined
            FROM appointments 
            WHERE DATE(appointment_date) = ?
        `, [today], (err, rows) => {
            db.close();
            if (err) {
                return res.json({ success: false, message: err.message });
            }

            const stats = rows[0] || { total: 0, accepted: 0, pending: 0, declined: 0 };
            res.json({
                success: true,
                data: {
                    total_today: stats.total,
                    accepted_today: stats.accepted || 0,
                    pending_today: stats.pending || 0,
                    declined_today: stats.declined || 0
                }
            });
        });
    } else {
        db.close();
        res.json({ success: false, message: 'Invalid action' });
    }
});

// DEBUG: Public appointments listing (local only) - for troubleshooting
app.get('/debug_appointments', (req, res) => {
    const db = new sqlite3.Database(DB_PATH);
    db.all('SELECT * FROM appointments ORDER BY created_at DESC', [], (err, rows) => {
        db.close();
        if (err) return res.json({ success: false, message: err.message });
        res.json({ success: true, count: rows.length, data: rows });
    });
});

// Initialize DB and Start Server
// Serve static files (after routes so API endpoints take precedence)
app.use(express.static(path.join(__dirname)));

initDatabase().then(() => {
    app.listen(PORT, () => {
        console.log(`\n${'='.repeat(60)}`);
        console.log(`✅ Server is running!`);
        console.log(`\n📱 Admin Panel: http://localhost:${PORT}/admin_login.html`);
        console.log(`📋 Appointment Form: http://localhost:${PORT}/appoinment.html`);
        console.log(`\n🔐 Default Credentials:`);
        console.log(`   Admin ID: admin`);
        console.log(`   Password: admin123`);
        console.log(`\n💾 Database: appointments.db`);
        console.log(`${'='.repeat(60)}\n`);
    });
}).catch(err => {
    console.error('Failed to initialize database:', err);
    process.exit(1);
});
