# 🏥 Sri Krishna Hospital - Admin Panel Setup Guide

## ✅ COMPLETE STEP-BY-STEP SETUP (5 MINUTES)

---

## **WHAT YOU NEED**
- ✅ Node.js (already installed - v24.13.0)
- ✅ Your doctor folder files

---

## **SETUP STEPS**

### **STEP 1: Open PowerShell**
1. Right-click your **doctor** folder
2. Click **"Open in Terminal"** (or **"Open PowerShell Window Here"**)
3. OR open PowerShell and run: `cd "C:\Users\hp\OneDrive\Desktop\doctor"`

### **STEP 2: Install Dependencies** 
Copy and paste this command:
```powershell
npm install
```
⏳ Wait 1-2 minutes (it will download express, sqlite3, etc.)

**You'll see:** `added 100+ packages`

### **STEP 3: Start the Server**
Copy and paste this command:
```powershell
npm start
```

**You should see:**
```
============================================================
✅ Server is running!

📱 Admin Panel: http://localhost:8000/admin_login.html
📋 Appointment Form: http://localhost:8000/appoinment.html

🔐 Default Credentials:
   Admin ID: admin
   Password: admin123

💾 Database: appointments.db
============================================================
```

✅ **Server is now running!**

---

## **USE THE SYSTEM**

### **Option A: Double-Click Startup** (Easiest)
1. Go to your **doctor** folder
2. Double-click: **`START_SERVER.bat`**
3. A window will open - server starts automatically
4. Leave it running in background

### **Option B: PowerShell Script**
1. Open PowerShell in doctor folder
2. Run: `.\START_SERVER.ps1`
3. Wait for "Server is running" message

### **Option C: Manual Start** (Every time)
1. Open PowerShell in doctor folder
2. Run: `npm start`

---

## **ACCESS THE SYSTEM**

### **Admin Panel** 📊
🔗 **URL:** http://localhost:8000/admin_login.html

**Login with:**
- **Admin ID:** `admin`
- **Password:** `admin123`

### **Appointment Form** 📋
🔗 **URL:** http://localhost:8000/appoinment.html

Patients can book appointments here, and they appear instantly in admin panel.

---

## **FEATURES**

### **Admin Dashboard Can:**
✅ Show today's appointments (default)  
✅ Sort by: Date, Name (A-Z), Doctor, Status, Latest  
✅ Filter by: Today / Week / Month / All  
✅ Filter by Status: Pending / Accepted / Declined  
✅ Change appointment status instantly  
✅ Delete appointments  
✅ View real-time statistics  
✅ Call patient directly (click phone number)

---

## **TROUBLESHOOTING**

### **Error: "npm: The term 'npm' is not recognized"**
**Solution:** Node.js may not be in PATH. Restart PowerShell or computer after installation.

### **Error: "Port 8000 already in use"**
**Solution:** Change port in `server.js` line 8:
```javascript
const PORT = 8001;  // Change 8000 to 8001
```

### **Files appear but database doesn't work**
**Solution:** 
1. Close server (Ctrl + C)
2. Delete `appointments.db` file
3. Run: `npm start` again
4. Fresh database will be created

### **Can't connect to http://localhost:8000**
**Solution:**
1. Make sure `npm start` shows "✅ Server is running"
2. Refresh browser
3. Check if port 8000 is blocked (try port 8001)

---

## **CHANGE ADMIN PASSWORD**

### **Step 1:** Open `server.js`
### **Step 2:** Find these lines (around line 50):
```javascript
const validAdmins = {
    'admin': 'admin123',
    'reception': 'reception123'
};
```

### **Step 3:** Change passwords:
```javascript
const validAdmins = {
    'admin': 'your_new_password_here',
    'reception': 'another_password_here'
};
```

### **Step 4:** Restart server (Ctrl + C, then `npm start`)

---

## **ADD MORE ADMIN USERS**

Open `server.js`, find the `validAdmins` section, and add:
```javascript
const validAdmins = {
    'admin': 'admin123',
    'reception1': 'password1',
    'reception2': 'password2',
    'manager': 'manager_password'
};
```

Then restart server.

---

## **FILE STRUCTURE**

```
doctor/
├── server.js                  ✅ Server file (NEW)
├── package.json              ✅ Dependencies (NEW)
├── START_SERVER.bat          ✅ Quick launcher (NEW)
├── START_SERVER.ps1          ✅ PowerShell launcher (NEW)
├── admin_login.html          ✅ Admin login page
├── admin_dashboard.html      ✅ Admin dashboard
├── appoinment.html           ✅ Patient booking form
├── style.css                 ✅ Styling
├── main.js                   ✅ JavaScript
├── appointments.db           ✅ Database (auto-created)
└── (other files)
```

---

## **WHAT HAPPENS**

1. **Server starts** → Creates/opens `appointments.db`
2. **Patient books appointment** → Data saved to database
3. **Admin logs in** → Sees all appointments
4. **Admin filters/sorts** → Updated in real-time
5. **Admin changes status** → Saved immediately

---

## **CLOUD DEPLOYMENT** (Later)

When ready for production (live website):
1. Use service like Heroku, Railway, or Render
2. Upload all files including `server.js`
3. They'll handle Node.js hosting
4. Database will persist in cloud

---

## **SUPPORT**

If you get stuck:
1. ✅ Check browser console (F12)
2. ✅ Check PowerShell output
3. ✅ Verify files are in doctor folder
4. ✅ Close and restart server
5. ✅ Clear browser cache (Ctrl + Shift + Delete)

---

## **QUICK START (TL;DR)**

```powershell
# 1. Open PowerShell in doctor folder
cd "C:\Users\hp\OneDrive\Desktop\doctor"

# 2. Install (first time only)
npm install

# 3. Start server (every time)
npm start

# 4. Open browser
http://localhost:8000/admin_login.html

# 5. Login with:
# Admin ID: admin
# Password: admin123
```

---

**Setup Complete!** 🎉

Your admin panel is now ready to use. Enjoy managing appointments! 👍
