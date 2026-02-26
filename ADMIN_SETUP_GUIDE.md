# Admin Panel Setup & Usage Guide

## System Overview

You now have a complete appointment management system that consists of:

1. **User Appointment Form** (`appoinment.html`) - Website visitors book appointments
2. **Admin Login** (`admin_login.html`) - Secure authentication
3. **Admin Dashboard** (`admin_dashboard.html`) - View, sort, filter, and manage appointments
4. **Backend APIs** (PHP files) - Handle all data processing

---

## Quick Start Guide

### Step 1: Initialize the Database (One-time only)
1. Open your browser and go to: `http://yourwebsite.com/admin_init_db.php`
2. You should see: "Database initialized successfully!"
3. This creates the SQLite database file (`appointments.db`)

### Step 2: Access Admin Panel
1. Go to: `http://yourwebsite.com/admin_login.html`
2. **Default Credentials:**
   - Admin ID: `admin`
   - Password: `admin123`
3. Click "Login to Admin Panel"

### Step 3: Manage Appointments
You now have full access to:
- ✅ View all appointment requests
- ✅ Sort by date, name, doctor, status, or latest
- ✅ Filter by date range (today, this week, this month, all)
- ✅ Filter by status (pending, accepted, declined)
- ✅ Update appointment status
- ✅ Delete appointments
- ✅ Real-time stats (today's totals)

---

## Features in Detail

### Dashboard Statistics (Top Section)
- **Total Today:** All appointments for today
- **Accepted:** Approved appointments
- **Pending:** Awaiting decision
- **Declined:** Rejected appointments

### Sorting Options
- **Date & Time:** Chronological order (earliest first)
- **Patient Name (A-Z):** Alphabetical by last name
- **Doctor Requested:** Group by doctor
- **Status:** Pending → Accepted → Declined
- **Latest First:** Newest bookings first

### Filtering Options
- **By Date:**
  - Today (default)
  - This Week
  - This Month
  - All Appointments

- **By Status:**
  - All Status (default)
  - Pending
  - Accepted
  - Declined

### Status Management
For each appointment, you can:
1. **Accept** ✓ - Confirm the appointment
2. **Pending** ⏳ - Leave for review
3. **Decline** ✗ - Reject the appointment

### Patient Information
Each appointment shows:
- Patient Name
- Contact Number (clickable to call)
- Email Address
- Preferred Date & Time
- Doctor Requested
- Medical Issue/Reason

---

## File Structure

```
doctor/
├── admin_login.html           # Login page for reception
├── admin_login.php            # Authentication backend
├── admin_dashboard.html       # Main admin panel
├── admin_api.php              # API for appointments
├── admin_check_auth.php       # Session check
├── admin_logout.php           # Logout handler
├── save_appointment.php       # Appointment form handler
├── admin_init_db.php          # Database initialization
├── appointments.db            # SQLite database (auto-created)
└── appoinment.html            # Patient booking form
```

---

## How Data Flows

1. **Patient Books Appointment**
   - Fills form in `appoinment.html`
   - Submits to `save_appointment.php`
   - Data saved in `appointments.db`

2. **Admin Views Appointments**
   - Logs in via `admin_login.html`
   - Dashboard loads from `admin_api.php`
   - Displays all appointments with filters/sorting
   - Can update status or delete appointments

---

## Security Notes

⚠️ **Important - Change Default Credentials**

Edit `admin_login.php` and update:
```php
$validAdmins = [
    'admin' => 'your_strong_password_here',
    'reception' => 'another_strong_password'
];
```

### Additional Security Tips:
- Use HTTPS (SSL certificate) on production
- Change admin credentials regularly
- Keep admin panel hidden from main navigation
- Add multi-factor authentication if needed
- Backup `appointments.db` regularly

---

## Customization

### Add More Admin Users
Edit `admin_login.php`:
```php
$validAdmins = [
    'admin' => 'password1',
    'reception1' => 'password2',
    'reception2' => 'password3'
];
```

### Change Doctor Name
Edit `admin_dashboard.html` and `appoinment.html`:
- Find the doctor dropdown
- Change "DR N Shiva Rami Reddy" to your doctor's name

### Modify Appointment Fields
To add/remove fields:
1. Create database migration (add columns to appointments table)
2. Update `appoinment.html` form
3. Update `admin_dashboard.html` to display new fields
4. Update `admin_api.php` to handle new fields

---

## Troubleshooting

### Issue: "Database error" when booking appointment
**Solution:** Run `admin_init_db.php` again to create/fix database

### Issue: Login fails with correct credentials
**Solution:** Clear browser cookies and cache, try incognito mode

### Issue: Appointments aren't showing up
**Solution:** 
1. Check if browser time/date is correct
2. Verify database exists: `appointments.db` should be in root folder
3. Check PHP version (needs 7.0+)

### Issue: Can't modify appointment status
**Solution:** Check file permissions on `admin_api.php` and database

---

## Advanced Features You Can Add

1. **Export to Excel/PDF** - Generate reports
2. **Email Notifications** - Auto-send confirmations
3. **SMS Reminders** - Alert patients before appointment
4. **Calendar View** - Visual appointment scheduling
5. **Appointment History** - Archive completed appointments
6. **Multi-doctor Support** - Different schedules per doctor
7. **Patient Portal** - Patients can view their own appointments
8. **Analytics** - Track booking trends, no-show rates

---

## Support & Updates

For questions or issues:
1. Check this guide first
2. Verify PHP version (7.0+) and SQLite support
3. Test locally before production deployment
4. Keep backups of `appointments.db`

---

## Default Login Credentials

| Field | Value |
|-------|-------|
| Admin ID | admin |
| Password | admin123 |

⚠️ **Change these immediately in production!**

---

**Last Updated:** February 27, 2026  
**Version:** 1.0  
**Status:** Production Ready
