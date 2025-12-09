# 🚀 ByteWave Application - Node.js Migration Guide

## 📚 Table of Contents
1. [Overview](#overview)
2. [Project Structure](#project-structure)
3. [Setup Instructions](#setup-instructions)
4. [API Documentation](#api-documentation)
5. [Key Changes from PHP](#key-changes-from-php)
6. [Frontend Integration](#frontend-integration)

---

## Overview

This is a complete migration of your ByteWave PHP application to **Node.js + Express**. 

### What Changed?
| Feature | PHP | Node.js |
|---------|-----|---------|
| Database Connection | MySQLi | MySQL2 (connection pool) |
| Authentication | PHP Sessions | JWT Tokens |
| Password Hashing | `password_hash()` | bcryptjs |
| Email Sending | PHPMailer | Nodemailer |
| Configuration | config.php | .env file |
| Database Init | create_database.php | initDb.js |

---

## Project Structure

```
nodejs/
├── server.js                 # 🔴 Main entry point (like index.php)
├── initDb.js                 # 🔴 Initialize database (like create_database.php)
├── package.json              # Dependencies & scripts
├── .env                      # 🔴 Environment variables (CREATE THIS!)
│
├── public/                   # Frontend files (HTML, CSS, JS)
│   ├── css/
│   └── js/
│
└── src/
    ├── config/
    │   └── database.js       # MySQL connection pool
    │
    ├── routes/               # API endpoints
    │   ├── authRoutes.js     # Login, Register, Verify TAC
    │   ├── userRoutes.js     # User profile, Admin functions
    │   ├── eventRoutes.js    # Event management
    │   └── chatbotRoutes.js  # Chatbot API
    │
    ├── controllers/          # Business logic (like your PHP functions)
    │   ├── authController.js
    │   ├── userController.js
    │   ├── eventController.js
    │   └── chatbotController.js
    │
    ├── middleware/           # Functions that run before routes
    │   └── auth.js           # JWT verification & role checking
    │
    └── utils/
        └── email.js          # Email sending functions
```

---

## Setup Instructions

### Step 1: Install Node.js
Download from: https://nodejs.org/ (v16 or higher)

Verify installation:
```bash
node --version
npm --version
```

### Step 2: Navigate to Project
```bash
cd nodejs
```

### Step 3: Install Dependencies
This installs all packages listed in `package.json`:
```bash
npm install
```

**What gets installed:**
- `express` - Web framework
- `mysql2` - Database driver
- `jsonwebtoken` - JWT authentication
- `bcryptjs` - Password hashing
- `nodemailer` - Email sending
- `dotenv` - Environment variables
- `nodemon` - Auto-restart during development

### Step 4: Create `.env` File
Copy the `.env` file and update with your settings:

```env
PORT=3000
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=gpsphere_db
DB_PORT=3307

JWT_SECRET=your_secret_key_here_change_in_production

EMAIL_HOST=smtp.gmail.com
EMAIL_PORT=587
EMAIL_USER=your_email@gmail.com
EMAIL_PASS=your_app_password

NODE_ENV=development
```

### Step 5: Initialize Database
Run this ONCE to create tables:
```bash
node initDb.js
```

**Output:**
```
✅ Database 'gpsphere_db' created or exists
✅ Table users created or exists
✅ Table events created or exists
✅ Table event_roles created or exists
✅ Default admin created (Email: admin@gpsphere.com | Password: Admin123!)
```

### Step 6: Start the Server
```bash
npm run dev
```

Or for production:
```bash
npm start
```

**Output:**
```
🚀 Server running on http://localhost:3000
📚 API Documentation:
   POST   /api/auth/register      - Register new user
   ...
```

---

## API Documentation

### 🔐 Authentication Endpoints

#### 1. **Register User**
```
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePass123!",
  "confirm": "SecurePass123!"
}

Response (201):
{
  "message": "Registration successful! Your account is pending admin approval."
}
```

#### 2. **Login (Step 1: Send TAC)**
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "SecurePass123!"
}

Response (200):
{
  "message": "TAC code sent to your email. Please verify to complete login.",
  "requiresTAC": true
}
```

#### 3. **Verify TAC (Step 2: Get Token)**
```
POST /api/auth/verify-tac
Content-Type: application/json

{
  "email": "john@example.com",
  "tac_code": "123456"
}

Response (200):
{
  "message": "Login successful",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "student"
  }
}
```

### 👤 User Endpoints

#### 4. **Get User Profile**
```
GET /api/user/profile
Authorization: Bearer <TOKEN>

Response (200):
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "role": "student",
  "status": "approved",
  "created_at": "2025-01-16T10:30:00Z"
}
```

#### 5. **Get All Users** (Admin only)
```
GET /api/user/all
Authorization: Bearer <ADMIN_TOKEN>

Response (200):
[
  { "id": 1, "name": "John", "email": "john@ex.com", "role": "student", ... },
  { "id": 2, "name": "Admin", "email": "admin@ex.com", "role": "admin", ... }
]
```

#### 6. **Approve User** (Admin only)
```
POST /api/user/approve
Authorization: Bearer <ADMIN_TOKEN>
Content-Type: application/json

{
  "userId": 5
}

Response (200):
{
  "message": "User approved successfully"
}
```

### 📅 Events Endpoints

#### 7. **Get All Events** (Public)
```
GET /api/events

Response (200):
[
  {
    "id": 1,
    "event_name": "GPS Workshop",
    "description": "Learn GPS basics",
    "event_date": "2025-02-15",
    "location": "Room 101",
    "status": "ongoing"
  }
]
```

#### 8. **Get Event Details** (Public)
```
GET /api/events/1

Response (200):
{
  "id": 1,
  "event_name": "GPS Workshop",
  "description": "Learn GPS basics",
  "event_date": "2025-02-15",
  "event_time": "14:00:00",
  "location": "Room 101",
  "director_needed": 1,
  "helper_needed": 5,
  "status": "ongoing",
  "roles": [
    { "id": 1, "event_id": 1, "role_name": "Director", "slots": 1 }
  ]
}
```

#### 9. **Create Event** (Admin only)
```
POST /api/events
Authorization: Bearer <ADMIN_TOKEN>
Content-Type: application/json

{
  "event_name": "GPS Workshop",
  "description": "Learn GPS basics",
  "event_date": "2025-02-15",
  "event_time": "14:00:00",
  "location": "Room 101",
  "director_needed": 1,
  "helper_needed": 5
}

Response (201):
{
  "message": "Event created successfully",
  "eventId": 1
}
```

#### 10. **Update Event** (Admin only)
```
PUT /api/events/1
Authorization: Bearer <ADMIN_TOKEN>
Content-Type: application/json

{
  "event_name": "Advanced GPS Workshop",
  "status": "finished"
}

Response (200):
{
  "message": "Event updated successfully"
}
```

#### 11. **Delete Event** (Admin only)
```
DELETE /api/events/1
Authorization: Bearer <ADMIN_TOKEN>

Response (200):
{
  "message": "Event deleted successfully"
}
```

### 🤖 Chatbot Endpoint

#### 12. **Send Message to Chatbot** (Public)
```
POST /api/chatbot
Content-Type: application/json

{
  "message": "What is GPS?"
}

Response (200):
{
  "reply": "🌍 GPSphere is a student organization focused on geospatial technology and mapping...",
  "timestamp": "2025-01-16T10:30:00Z"
}
```

---

## Key Changes from PHP

### 1. **Database Connection**

**PHP (OLD):**
```php
$conn = new mysqli($host, $user, $pass, $dbname, $port);
$result = $conn->query("SELECT * FROM users");
```

**Node.js (NEW):**
```javascript
const pool = require('../config/database');
const [rows] = await pool.query("SELECT * FROM users");
```

### 2. **Authentication**

**PHP (OLD):**
```php
session_start();
$_SESSION['email'] = $email;
if (isset($_SESSION['email'])) { /* protected */ }
```

**Node.js (NEW):**
```javascript
const token = jwt.sign({ id, email }, JWT_SECRET);
// Send token to frontend
// Frontend sends: Authorization: Bearer <TOKEN>
```

### 3. **Password Hashing**

**PHP (OLD):**
```php
$hash = password_hash($password, PASSWORD_DEFAULT);
password_verify($input, $hash);
```

**Node.js (NEW):**
```javascript
const bcrypt = require('bcryptjs');
const hash = await bcrypt.hash(password, 10);
const isValid = await bcrypt.compare(input, hash);
```

### 4. **Email Sending**

**PHP (OLD):**
```php
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->sendMail();
```

**Node.js (NEW):**
```javascript
const nodemailer = require('nodemailer');
const transporter = nodemailer.createTransport({...});
await transporter.sendMail({...});
```

### 5. **Environment Variables**

**PHP (OLD):**
```php
$host = "localhost";
$user = "root";
```

**Node.js (NEW):**
```javascript
require('dotenv').config();
const host = process.env.DB_HOST;
const user = process.env.DB_USER;
```

---

## Frontend Integration

### How to Use JWT Tokens in Frontend

**JavaScript Example:**
```javascript
// 1. Register
async function register(name, email, password) {
  const res = await fetch('http://localhost:3000/api/auth/register', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name, email, password, confirm: password })
  });
  return res.json();
}

// 2. Login
async function login(email, password) {
  const res = await fetch('http://localhost:3000/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });
  return res.json();
}

// 3. Verify TAC
async function verifyTAC(email, tac_code) {
  const res = await fetch('http://localhost:3000/api/auth/verify-tac', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, tac_code })
  });
  const data = await res.json();
  // Store token in localStorage
  localStorage.setItem('token', data.token);
  return data;
}

// 4. Fetch Protected Data with Token
async function getUserProfile() {
  const token = localStorage.getItem('token');
  const res = await fetch('http://localhost:3000/api/user/profile', {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  return res.json();
}

// 5. Logout
function logout() {
  localStorage.removeItem('token');
}
```

---

## 🐛 Troubleshooting

### Error: "Cannot find module 'express'"
```bash
npm install
```

### Error: "Connection failed"
- Check `.env` file settings
- Verify MySQL is running
- Check DB_PORT is correct (usually 3306 or 3307)

### Error: "Email sending failed"
- Verify Gmail credentials
- Use [App Password](https://support.google.com/accounts/answer/185833) instead of regular password
- Enable "Less secure app access"

### Port 3000 already in use
```bash
# Use different port
PORT=3001 npm start
```

---

## 📝 Next Steps

1. ✅ Install dependencies (`npm install`)
2. ✅ Create `.env` file with your settings
3. ✅ Initialize database (`node initDb.js`)
4. ✅ Start server (`npm run dev`)
5. ✅ Test endpoints using Postman/Thunder Client
6. ✅ Update frontend to use new API endpoints
7. ✅ Deploy to production server

---

## 🎯 Questions?

- Check API responses for error messages
- Review console logs on server
- Enable debug mode in .env (NODE_ENV=development)

---

Happy coding! 🚀
