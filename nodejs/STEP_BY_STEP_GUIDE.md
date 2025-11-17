# 📚 COMPLETE STEP-BY-STEP SETUP GUIDE

## 🎯 Your Goal: Migrate from PHP to Node.js

This guide will take you through **every single step** needed to run your ByteWave application on Node.js.

---

## ⏱️ Estimated Time: 15 minutes

---

## PART 1: PREREQUISITES (5 minutes)

### Step 1.1: Check if Node.js is Installed

Open Terminal and run:
```bash
node --version
npm --version
```

**Expected Output:**
```
v16.0.0 (or higher)
8.0.0 (or higher)
```

**If NOT installed:**
- Download from: https://nodejs.org/
- Choose LTS (Long Term Support) version
- Install and restart Terminal

### Step 1.2: Check if MySQL is Running

Your application needs MySQL running on port 3307.

**To check:**
```bash
# On Mac/Linux
ps aux | grep mysql

# On Windows
tasklist | findstr mysql
```

You should see a mysql process running.

**If NOT running:**
- On Mac: Open MySQL Workbench or use: `brew services start mysql`
- On Windows: Use MySQL Workbench or Command Prompt
- On Linux: `sudo systemctl start mysql`

---

## PART 2: SETUP NODE.JS PROJECT (10 minutes)

### Step 2.1: Open Terminal & Navigate to Project

```bash
cd /Users/hemabhaskarayyappa/Documents/github/ByteWave-Application-Development/nodejs
```

### Step 2.2: Install Dependencies

This downloads all required packages (express, mysql2, nodemailer, etc.)

```bash
npm install
```

**What happens:**
- Creates `node_modules/` folder with all packages
- Takes 1-2 minutes
- You should see: `added XXX packages`

### Step 2.3: Create `.env` Configuration File

**Why?** This file contains sensitive information (passwords, email credentials) that should never be shared.

**File location:** `/nodejs/.env` (at root level)

**Content:** Copy this exactly and update with YOUR settings:

```env
# Server Port
PORT=3000

# Database Connection
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=gpsphere_db
DB_PORT=3307

# JWT Authentication Secret (change this!)
JWT_SECRET=super_secret_key_change_me_in_production_12345

# Email Configuration (Gmail)
EMAIL_HOST=smtp.gmail.com
EMAIL_PORT=587
EMAIL_USER=chengjiesu310@gmail.com
EMAIL_PASS=wyzcvlgiaeztnjem

# Environment
NODE_ENV=development
```

**⚠️ Important:**
- For `EMAIL_PASS`: Use [Gmail App Password](https://support.google.com/accounts/answer/185833), NOT your regular password
- Change `JWT_SECRET` in production
- Never commit this file to GitHub

**To create the file:**
1. Open VS Code
2. File → New File
3. Save as `.env` in the `nodejs` folder
4. Paste the content above
5. Update with your settings

### Step 2.4: Initialize Database

This creates all tables and inserts default admin account.

```bash
node initDb.js
```

**Expected Output:**
```
✅ Database 'gpsphere_db' created or exists
✅ Table users created or exists
✅ Table events created or exists
✅ Table event_roles created or exists
✅ Default admin created (Email: admin@gpsphere.com | Password: Admin123!)
```

**If you see errors:**
- Check MySQL is running
- Check `.env` file has correct database settings
- Check database port (usually 3307 or 3306)

### Step 2.5: Start the Server

```bash
npm run dev
```

**Expected Output:**
```
🚀 Server running on http://localhost:3000
📚 API Documentation:
   POST   /api/auth/register      - Register new user
   POST   /api/auth/login         - Login user (sends TAC)
   POST   /api/auth/verify-tac    - Verify TAC and get token
   GET    /api/user/profile       - Get user profile
   ...
```

**You're running! 🎉**

Leave this terminal open. The server is now listening for requests.

---

## PART 3: TEST THE APPLICATION (in new terminal)

### Step 3.1: Test Server Health

Open a NEW terminal (don't stop the server) and run:

```bash
curl http://localhost:3000/api/health
```

**Expected Response:**
```json
{"status":"Server is running ✅"}
```

### Step 3.2: Test Registration

Create a new user:

```bash
curl -X POST http://localhost:3000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "TestPass123!",
    "confirm": "TestPass123!"
  }'
```

**Expected Response:**
```json
{
  "message": "Registration successful! Your account is pending admin approval."
}
```

### Step 3.3: Test Login with Default Admin

```bash
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@gpsphere.com",
    "password": "Admin123!"
  }'
```

**Expected Response:**
```json
{
  "message": "TAC code sent to your email. Please verify to complete login.",
  "requiresTAC": true
}
```

### Step 3.4: Check TAC Code

**Where to find it:**
- Check your email (admin@gpsphere.com)
- OR check server console logs (where npm run dev is running)

You should see something like: `TAC Code: 123456`

### Step 3.5: Verify TAC and Get Token

```bash
curl -X POST http://localhost:3000/api/auth/verify-tac \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@gpsphere.com",
    "tac_code": "123456"
  }'
```

**Expected Response:**
```json
{
  "message": "Login successful",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "name": "System Admin",
    "email": "admin@gpsphere.com",
    "role": "admin"
  }
}
```

**Save the token!** You'll use it for protected requests.

### Step 3.6: Test Protected Route

Replace `YOUR_TOKEN` with the token from Step 3.5:

```bash
curl http://localhost:3000/api/user/profile \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected Response:**
```json
{
  "id": 1,
  "name": "System Admin",
  "email": "admin@gpsphere.com",
  "role": "admin",
  "status": "approved",
  "created_at": "2025-01-16T10:30:00.000Z"
}
```

✅ **If you got this response, everything works!**

---

## PART 4: TEST VIA WEB BROWSER (Optional)

### Step 4.1: Open Test Page

In your browser:
```
http://localhost:3000
```

You should see a form to test:
- Register new user
- Login
- Chat with bot

### Step 4.2: Test Registration Form

1. Fill in the form
2. Click "Register"
3. See response below

### Step 4.3: Test Login

1. Use `admin@gpsphere.com` / `Admin123!`
2. Click "Login"
3. Check email for TAC code
4. Enter TAC code
5. Get JWT token

---

## PART 5: UNDERSTAND THE ARCHITECTURE

### Request Flow

```
User Request
    ↓
Express Server (server.js)
    ↓
Route Handler (src/routes/*.js)
    ↓
Controller Function (src/controllers/*.js)
    ↓
Database Query (src/config/database.js)
    ↓
MySQL Database (gpsphere_db)
    ↓
Response sent back to user
```

### Folder Structure Explained

```
nodejs/
├── server.js              ← Main application entry point
├── initDb.js              ← One-time database setup
├── package.json           ← List of dependencies
├── .env                   ← Configuration (NEVER commit!)
├── .gitignore             ← Files to ignore in git
│
├── src/
│   ├── config/database.js       ← MySQL connection pool
│   │                              (like config.php)
│   │
│   ├── controllers/              ← Business logic (PHP functions)
│   │   ├── authController.js    (login.php, register.php)
│   │   ├── userController.js    (dashboard.php, profile.php)
│   │   ├── eventController.js   (event management)
│   │   └── chatbotController.js (chatbot.php)
│   │
│   ├── routes/                   ← API endpoints (URL mappings)
│   │   ├── authRoutes.js
│   │   ├── userRoutes.js
│   │   ├── eventRoutes.js
│   │   └── chatbotRoutes.js
│   │
│   ├── middleware/auth.js        ← JWT verification
│   │                               (replaces session_start())
│   │
│   └── utils/email.js            ← Email sending (replaces PHPMailer)
│
└── public/
    └── index.html               ← Test page
```

---

## PART 6: API ENDPOINTS QUICK REFERENCE

### Authentication
```
POST   /api/auth/register        - Create account
POST   /api/auth/login           - Send TAC code
POST   /api/auth/verify-tac      - Verify TAC & get token
```

### Users
```
GET    /api/user/profile         - Get my profile
GET    /api/user/all             - Get all users (admin only)
POST   /api/user/approve         - Approve user (admin only)
```

### Events
```
GET    /api/events               - Get all events
GET    /api/events/:id           - Get event details
POST   /api/events               - Create event (admin only)
PUT    /api/events/:id           - Update event (admin only)
DELETE /api/events/:id           - Delete event (admin only)
```

### Chatbot
```
POST   /api/chatbot              - Send message to bot
```

---

## PART 7: NEXT STEPS

### Update Your Frontend

Your HTML/CSS/JS needs to call the new API endpoints instead of PHP files.

**Old way (PHP):**
```html
<form action="login.php" method="POST">
```

**New way (Node.js):**
```javascript
fetch('/api/auth/login', {
  method: 'POST',
  body: JSON.stringify({email, password})
})
```

### Deploy to Production

When ready, deploy to:
- Heroku (free tier available)
- DigitalOcean
- AWS
- Azure

---

## PART 8: TROUBLESHOOTING

### ❌ "npm: command not found"
**Fix:** Install Node.js from https://nodejs.org/

### ❌ "Cannot find module 'express'"
**Fix:** Run `npm install`

### ❌ "Connection refused (MySQL)"
**Fix:** 
- Make sure MySQL is running
- Check `.env` DB_HOST and DB_PORT are correct

### ❌ "Port 3000 already in use"
**Fix:** Run `PORT=3001 npm start`

### ❌ "Email not sending"
**Fix:**
- For Gmail: Use App Password (not regular password)
- Check .env EMAIL settings are correct

### ❌ "Token is invalid"
**Fix:**
- Make sure you're sending the token in Authorization header
- Format: `Authorization: Bearer YOUR_TOKEN`
- Token expires after 24 hours

---

## ✅ Checklist

- [ ] Node.js installed
- [ ] MySQL running
- [ ] npm install (dependencies installed)
- [ ] .env file created with correct settings
- [ ] node initDb.js (database initialized)
- [ ] npm run dev (server running)
- [ ] curl /api/health (server responds)
- [ ] Register test user
- [ ] Login with admin account
- [ ] Verify TAC
- [ ] Get user profile with token
- [ ] Test web page at http://localhost:3000

**All done? 🎉 Your Node.js application is ready!**

---

## 📞 Still Need Help?

1. **Read:** MIGRATION_GUIDE.md (detailed explanations)
2. **Test:** API_TESTING_GUIDE.sh (curl examples)
3. **View:** Web page at http://localhost:3000 (interactive tests)
4. **Check:** Console logs where npm run dev is running

---

Happy coding! 🚀
