# 🚀 Running GPS UTM System with XAMPP

This guide explains how to run the Node.js application using XAMPP's MySQL database.

## ⚠️ Important Note

**This is a Node.js application**, not a PHP application. XAMPP is used **only for MySQL database**. The web server (Apache) is **not needed**.

---

## 📋 Prerequisites

1. **XAMPP** installed (for MySQL)
2. **Node.js** (v14 or higher) installed
3. **npm** (comes with Node.js)

---

## 🔧 Step-by-Step Setup

### Step 1: Start XAMPP MySQL

1. Open **XAMPP Control Panel**
2. Click **Start** next to **MySQL**
3. Wait until MySQL status shows **green** (running)

**Note:** You do **NOT** need to start Apache.

### Step 2: Check Your MySQL Port

XAMPP MySQL typically runs on port **3306** (default).

**To check your port:**
- Look at XAMPP Control Panel → MySQL → Port
- Or check `C:\xampp\mysql\bin\my.ini` (Windows) or `/Applications/XAMPP/etc/my.cnf` (Mac)

**Common ports:**
- **3306** - Default XAMPP MySQL port
- **3307** - If you have another MySQL instance running

### Step 3: Navigate to Project Directory

Open **Command Prompt** (Windows) or **Terminal** (Mac/Linux) and navigate to the project:

```bash
cd C:\xampp\htdocs\ByteWave-Application-Development-main\nodejs
```

Or if your project is in a different location:
```bash
cd path\to\your\project\nodejs
```

### Step 4: Install Node.js Dependencies

```bash
npm install
```

This will install all required packages (express, mysql2, nodemailer, etc.).

**Expected output:**
```
added XXX packages
```

### Step 5: Create `.env` Configuration File

Create a file named `.env` in the `nodejs` folder with the following content:

```env
# Server Configuration
PORT=3000

# Database Configuration (XAMPP MySQL)
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=gpsphere_db
DB_PORT=3306

# ⚠️ IMPORTANT: Change DB_PORT if your XAMPP MySQL uses a different port
# If you see port conflicts, check XAMPP Control Panel for the actual port
# Common alternatives: 3307, 3308

# JWT Secret (change this in production!)
JWT_SECRET=your-secret-key-change-this-in-production

# Email Configuration (Gmail)
EMAIL_HOST=smtp.gmail.com
EMAIL_PORT=587
EMAIL_USER=your_email@gmail.com
EMAIL_PASS=your_gmail_app_password

# TAC Test Mode (set to 'true' for development)
TAC_TEST_MODE=true
```

**Important Settings:**
- **DB_PORT**: Usually `3306` for XAMPP. If you have port conflicts, use `3307` or check XAMPP settings.
- **DB_PASSWORD**: Leave empty if XAMPP MySQL has no password (default).
- **EMAIL_PASS**: Use [Gmail App Password](https://myaccount.google.com/apppasswords), NOT your regular Gmail password.

### Step 6: Initialize Database

Run the database initialization script:

```bash
node scripts/initDb.js
```

**Expected output:**
```
Starting database initialization...
DB_HOST: localhost
DB_USER: root
Attempting MySQL connection...
✅ Connected to MySQL
✅ Database 'gpsphere_db' created or exists
✅ Table users created or exists
✅ Table events created or exists
✅ Table event_roles created or exists
✅ Table event_applications created or exists
✅ Default admin created (Email: admin@gpsphere.com | Password: Admin123!)
🎉 Database initialization complete!
```

**If you see connection errors:**
- ✅ Check MySQL is running in XAMPP Control Panel
- ✅ Verify `DB_PORT` in `.env` matches XAMPP MySQL port
- ✅ Check `DB_PASSWORD` is correct (usually empty for XAMPP)
- ✅ Try accessing phpMyAdmin: `http://localhost/phpmyadmin`

### Step 7: Start Node.js Server

```bash
node server.js
```

Or for development with auto-restart:
```bash
npm run dev
```

**Expected output:**
```
✅ Connected to MySQL database
🚀 Server running on http://localhost:3000
📚 API Documentation:
   POST   /api/auth/register      - Register new user
   POST   /api/auth/login         - Login user (sends TAC)
   POST   /api/auth/verify-tac    - Verify TAC and get token
   ...
```

**✅ Server is now running!**

### Step 8: Access the Application

Open your web browser and visit:

- **Homepage**: http://localhost:3000/homepage.html
- **Login/Register**: http://localhost:3000/login_register.html

---

## 🔑 Default Admin Account

After database initialization, you can login with:

- **Email**: `admin@gpsphere.com`
- **Password**: `Admin123!`

---

## 🛠️ Troubleshooting

### Problem: "Cannot connect to MySQL"

**Solutions:**
1. ✅ Check XAMPP Control Panel → MySQL is **green** (running)
2. ✅ Verify `DB_PORT` in `.env` matches XAMPP MySQL port
3. ✅ Check `DB_PASSWORD` (usually empty for XAMPP default)
4. ✅ Try accessing phpMyAdmin: `http://localhost/phpmyadmin`

### Problem: "Port 3306 already in use"

**Solution:**
- Your XAMPP MySQL might be using a different port
- Check XAMPP Control Panel for the actual port
- Update `DB_PORT` in `.env` to match (e.g., `3307`)

### Problem: "Cannot find module 'express'"

**Solution:**
```bash
cd nodejs
npm install
```

### Problem: "Access denied for user 'root'@'localhost'"

**Solutions:**
1. Check `DB_PASSWORD` in `.env` (should be empty for XAMPP default)
2. If you set a MySQL password, update `DB_PASSWORD` in `.env`
3. Reset MySQL password in XAMPP if needed

### Problem: Server won't start on port 3000

**Solution:**
- Change `PORT` in `.env` to a different port (e.g., `3001`)
- Access application at `http://localhost:3001`

---

## 📊 Architecture Overview

```
┌─────────────────┐
│   Web Browser   │
│  (localhost:3000)│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Node.js Server │  ← Runs separately (not on Apache)
│  (Express.js)   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  XAMPP MySQL    │  ← Only this part uses XAMPP
│  (Port 3306)    │
└─────────────────┘
```

**Key Points:**
- ✅ **XAMPP MySQL**: Provides database
- ✅ **Node.js Server**: Handles web requests (runs on port 3000)
- ❌ **XAMPP Apache**: Not needed (Node.js replaces it)

---

## 🔄 Daily Usage

### Starting the System

1. **Start XAMPP MySQL** (XAMPP Control Panel → Start MySQL)
2. **Start Node.js Server**:
   ```bash
   cd nodejs
   node server.js
   ```
3. **Open Browser**: http://localhost:3000/homepage.html

### Stopping the System

1. **Stop Node.js Server**: Press `Ctrl+C` in terminal
2. **Stop XAMPP MySQL** (optional): XAMPP Control Panel → Stop MySQL

---

## 📝 Quick Reference

| Component | Port | Location |
|-----------|------|----------|
| Node.js Server | 3000 | `nodejs/server.js` |
| XAMPP MySQL | 3306 (default) | XAMPP Control Panel |
| XAMPP phpMyAdmin | 80 | http://localhost/phpmyadmin |

---

## ✅ Verification Checklist

Before running, ensure:

- [ ] XAMPP MySQL is running (green in Control Panel)
- [ ] Node.js is installed (`node --version`)
- [ ] Dependencies installed (`npm install` completed)
- [ ] `.env` file created with correct settings
- [ ] Database initialized (`node scripts/initDb.js` succeeded)
- [ ] Server starts without errors (`node server.js`)

---

## 🎉 You're All Set!

Your GPS UTM system is now running with XAMPP MySQL. The Node.js server handles all web requests, while XAMPP provides the MySQL database backend.

For more information, see:
- [Main README](../README.md)
- [Step-by-Step Guide](./STEP_BY_STEP_GUIDE.md)
- [Email Setup](./EMAIL_SETUP.md)

