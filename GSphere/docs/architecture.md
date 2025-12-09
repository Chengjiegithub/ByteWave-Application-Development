# 📊 BYTEWAVE NODEJS - VISUAL ARCHITECTURE

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        CLIENT (Browser)                      │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │  index.html (Web Interface)                             │ │
│  │  ├── Register Form ──► Fetch /api/auth/register        │ │
│  │  ├── Login Form ────► Fetch /api/auth/login            │ │
│  │  ├── TAC Verify ────► Fetch /api/auth/verify-tac       │ │
│  │  └── Chatbot ───────► Fetch /api/chatbot               │ │
│  └─────────────────────────────────────────────────────────┘ │
│           │ JSON (HTTP/REST)                                  │
└───────────┼───────────────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────────────────┐
│                    EXPRESS SERVER                            │
│  Port: 3000 (localhost:3000)                                │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ server.js (Entry Point)                             │   │
│  └─────────────────────────────────────────────────────┘   │
│                         │                                   │
│         ┌───────────────┼───────────────┐                  │
│         ▼               ▼               ▼                  │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐           │
│  │ Auth Routes│  │User Routes │  │Event Routes│ Chatbot   │
│  └────────────┘  └────────────┘  └────────────┘ Routes    │
│                         │                                   │
│         ┌───────────────┼───────────────┐                  │
│         ▼               ▼               ▼                  │
│  ┌──────────────────┐  ┌───────────────────┐              │
│  │  Controllers     │  │   Middleware      │              │
│  │ - auth.js        │  │  - auth.js (JWT)  │              │
│  │ - user.js        │  │                   │              │
│  │ - event.js       │  │                   │              │
│  │ - chatbot.js     │  │                   │              │
│  └──────────────────┘  └───────────────────┘              │
│         │                       │                          │
│         │ Business Logic        │ Verification            │
│         │                       │                          │
│  ┌──────────────────────────────────┐                     │
│  │     Utils & Services              │                     │
│  │  - email.js (Nodemailer)          │                     │
│  │  - password hashing (bcrypt)      │                     │
│  │  - JWT token generation           │                     │
│  └──────────────────────────────────┘                     │
│         │                                                  │
│         ▼                                                  │
│  ┌──────────────────┐                                     │
│  │  MySQL Connection│                                     │
│  │  (database.js)   │                                     │
│  └──────────────────┘                                     │
└─────────────────────────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────────────────┐
│                    MYSQL DATABASE                           │
│  Host: localhost                                            │
│  Port: 3307                                                 │
│  Database: gpsphere_db                                      │
│                                                             │
│  Tables:                                                    │
│  ├── users (id, name, email, password, role, status, ...)  │
│  ├── events (id, event_name, description, event_date, ...) │
│  └── event_roles (id, event_id, role_name, slots, ...)     │
└─────────────────────────────────────────────────────────────┘
```

---

## Request Flow Example: User Login

```
USER SUBMITS LOGIN FORM
        │
        ▼
Browser sends JSON:
{
  "email": "admin@gpsphere.com",
  "password": "Admin123!"
}
        │
        ▼
POST /api/auth/login
        │
        ▼
Express Route Handler (authRoutes.js)
        │
        ▼
authController.login()
        │
        ├─ Validate email & password
        │
        ├─ Query MySQL: SELECT * FROM users WHERE email = ?
        │
        ├─ Verify password with bcrypt.compare()
        │
        ├─ Generate 6-digit TAC code
        │
        ├─ Store TAC in database
        │
        ├─ Send email via Nodemailer (sendTACEmail)
        │
        ▼
Return JSON Response:
{
  "message": "TAC code sent to your email",
  "requiresTAC": true
}
        │
        ▼
Browser shows: "Check your email for TAC code"
        │
        ▼
USER ENTERS TAC CODE
        │
        ▼
POST /api/auth/verify-tac with TAC code
        │
        ▼
authController.verifyTAC()
        │
        ├─ Query MySQL: SELECT * FROM users WHERE email = ? AND tac_code = ?
        │
        ├─ Check if TAC expired
        │
        ├─ Generate JWT token
        │
        ├─ Clear TAC from database
        │
        ▼
Return JSON Response:
{
  "message": "Login successful",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": { "id": 1, "name": "Admin", "email": "...", "role": "admin" }
}
        │
        ▼
Browser stores token in localStorage
        │
        ▼
USER LOGGED IN ✅
```

---

## Protected Route Example: Get User Profile

```
Frontend JavaScript:
const token = localStorage.getItem('token');
fetch('/api/user/profile', {
  headers: { 'Authorization': 'Bearer ' + token }
})
        │
        ▼
GET /api/user/profile with Authorization header
        │
        ▼
Middleware: auth.verifyToken()
        │
        ├─ Extract token from header
        │
        ├─ Verify token signature
        │
        ├─ Check if token expired
        │
        ├─ Extract user data from token
        │
        ├─ Attach user to request: req.user
        │
        ▼
If token valid:
  → Continue to controller
  
If token invalid:
  → Return 401 Unauthorized
        │
        ▼
userController.getUserProfile()
        │
        ├─ Get userId from req.user
        │
        ├─ Query: SELECT * FROM users WHERE id = ?
        │
        ▼
Return JSON:
{
  "id": 1,
  "name": "System Admin",
  "email": "admin@gpsphere.com",
  "role": "admin",
  "status": "approved",
  "created_at": "2025-01-16T10:30:00Z"
}
        │
        ▼
✅ PROTECTED DATA RETURNED
```

---

## File Organization

```
nodejs/
│
├── 📄 server.js ─────────────► Main Express Application
│
├── 📄 initDb.js ─────────────► Database Setup Script
│
├── 📄 package.json ──────────► Dependencies & Scripts
│
├── 📄 .env ──────────────────► Configuration (NEVER commit!)
│
├── 📁 src/
│   │
│   ├── 📁 config/
│   │   └── database.js ──────► MySQL Connection Pool
│   │                         (Like: config.php)
│   │
│   ├── 📁 controllers/
│   │   ├── authController.js ─► Logic for auth (Like: login.php)
│   │   ├── userController.js ─► Logic for users (Like: dashboard.php)
│   │   ├── eventController.js ► Logic for events (Like: admin_dashboard.php)
│   │   └── chatbotController.js ► Logic for chatbot (Like: chatbot.php)
│   │
│   ├── 📁 routes/
│   │   ├── authRoutes.js ────► /api/auth/* endpoints
│   │   ├── userRoutes.js ────► /api/user/* endpoints
│   │   ├── eventRoutes.js ───► /api/events/* endpoints
│   │   └── chatbotRoutes.js ─► /api/chatbot endpoint
│   │
│   ├── 📁 middleware/
│   │   └── auth.js ─────────► JWT verification (Like: session_start())
│   │
│   └── 📁 utils/
│       └── email.js ────────► Nodemailer (Like: PHPMailer)
│
├── 📁 public/
│   └── index.html ──────────► Test Web Interface
│
└── 📁 Documentation/
    ├── README.md ────────────► Quick Start (5 min)
    ├── STEP_BY_STEP_GUIDE.md ► Complete Setup (15 min)
    ├── MIGRATION_GUIDE.md ───► Detailed Reference
    ├── API_TESTING_GUIDE.sh ─► Curl Commands
    ├── EMAIL_SETUP.md ───────► Email Configuration
    └── MIGRATION_SUMMARY.md ─► This Document
```

---

## API Endpoints Map

```
Authentication Endpoints:
  POST /api/auth/register       ─► Create Account
  POST /api/auth/login          ─► Send TAC Code
  POST /api/auth/verify-tac     ─► Verify & Get Token

User Endpoints:
  GET  /api/user/profile        ─► Get My Profile (Protected)
  GET  /api/user/all            ─► Get All Users (Admin)
  POST /api/user/approve        ─► Approve User (Admin)

Event Endpoints:
  GET    /api/events            ─► Get All Events
  GET    /api/events/:id        ─► Get Event Details
  POST   /api/events            ─► Create Event (Admin)
  PUT    /api/events/:id        ─► Update Event (Admin)
  DELETE /api/events/:id        ─► Delete Event (Admin)

Chatbot Endpoint:
  POST /api/chatbot             ─► Send Message
```

---

## Technology Stack

```
┌────────────────────────────────────────────────────────────┐
│ FRONTEND                                                   │
├────────────────────────────────────────────────────────────┤
│ HTML5, CSS3, JavaScript (Vanilla or React/Vue)             │
│ Communicates via REST API (JSON)                           │
│ Stores JWT token in localStorage                           │
└────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│ BACKEND                                                    │
├────────────────────────────────────────────────────────────┤
│ Node.js v16+ with Express.js                               │
│ Authentication: JWT (jsonwebtoken)                         │
│ Password Hashing: bcryptjs                                 │
│ Email: Nodemailer                                          │
│ Environment: dotenv                                        │
│ Validation: express-validator                              │
└────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│ DATABASE                                                   │
├────────────────────────────────────────────────────────────┤
│ MySQL 5.7+                                                 │
│ Driver: mysql2/promise (supports async/await)              │
│ Connection: Pool (multiple simultaneous connections)       │
│ Tables: users, events, event_roles                         │
└────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│ DEPLOYMENT                                                 │
├────────────────────────────────────────────────────────────┤
│ Can deploy to: Heroku, DigitalOcean, AWS, Azure, etc.     │
│ Starts with: npm start (production) or npm run dev (dev)   │
│ Port: 3000 (configurable via .env PORT)                   │
└────────────────────────────────────────────────────────────┘
```

---

## Comparison: PHP vs Node.js

```
┌────────────────┬──────────────────┬──────────────────┐
│ Feature        │ PHP (OLD)        │ Node.js (NEW)    │
├────────────────┼──────────────────┼──────────────────┤
│ Language       │ PHP              │ JavaScript       │
│ Framework      │ None (Vanilla)   │ Express.js       │
│ Entry Point    │ index.php        │ server.js        │
│ Structure      │ Files in root    │ Organized       │
│ Authentication │ $_SESSION        │ JWT Tokens      │
│ Routing        │ .php files       │ routes/         │
│ Logic          │ Mixed in .php    │ controllers/    │
│ Database       │ MySQLi (mysqli)  │ MySQL2 (pool)   │
│ Hashing        │ password_hash()  │ bcryptjs        │
│ Email          │ PHPMailer        │ Nodemailer      │
│ Config         │ Hard-coded       │ .env file       │
│ Async          │ Limited          │ Full support    │
│ Scalability    │ Low              │ High            │
└────────────────┴──────────────────┴──────────────────┘
```

---

## Next Steps

1. Read: README.md (5 min)
2. Setup: STEP_BY_STEP_GUIDE.md (15 min)
3. Test: API_TESTING_GUIDE.sh
4. Update Frontend to use new API
5. Deploy to production

Happy coding! 🚀
