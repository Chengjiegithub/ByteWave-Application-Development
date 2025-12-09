# GPSphere - GPS UTM Event Management System

A modern digital platform for the **Gerakan Pengguna Siswa (GPS)** - Student Consumer Movement at Universiti Teknologi Malaysia (UTM) Johor.

## 🌟 Overview

GPSphere is a comprehensive event management system that enables students to register, become members, and participate in GPS UTM events. The platform features role-based access control, two-factor authentication, and an AI-powered chatbot assistant.

## 🚀 Features

### Authentication & Security
- **Student Registration** with secure password hashing (bcrypt)
- **Two-Factor Authentication (TAC)** - Time Authentication Code system
- **TAC Test Mode** - For local development (TAC appears on screen)
- **TAC Gmail Mode** - Production mode (TAC sent via email)
- **Session Management** - Secure user sessions

### User Roles
- **Student** - Can register and view basic information
- **Member** - Approved students who can participate in events
- **Admin** - Full system access, user approval, event management

### Event Management
- Create, update, and manage events
- Role-based event participation (Director, Secretary, Technical Crew, Helper, etc.)
- Event application and approval system
- Real-time event information

### AI Chatbot Assistant
- Interactive chat interface with message history
- Context-aware responses
- Real-time event data integration
- Suggestion buttons for quick responses
- Typing indicators and smooth animations

### Email Notifications
- Automated welcome emails
- TAC verification codes
- Account approval notifications
- Event updates

## 📁 Project Structure

```
GSphere/
├── package.json              # Root package.json (convenience scripts)
├── GSphere/                  # Main application directory
│   ├── docs/                 # Documentation files
│   │   ├── chatbot-features.md
│   │   ├── architecture.md
│   │   ├── api-testing-guide.sh
│   │   ├── setup/
│   │   │   ├── email-setup.md
│   │   │   └── step-by-step-guide.md
│   │   └── ...
│   ├── public/               # Frontend files
│   │   ├── css/             # Stylesheets
│   │   ├── images/          # Logo and images
│   │   ├── js/              # Client-side JavaScript
│   │   ├── *.html           # HTML pages
│   │   └── chatbot_widget.html
│   ├── scripts/             # Utility scripts
│   │   └── initDb.js        # Database initialization
│   ├── src/                 # Backend source code
│   │   ├── config/          # Configuration files
│   │   ├── controllers/     # Route controllers
│   │   ├── middleware/     # Express middleware
│   │   ├── routes/          # API routes
│   │   └── utils/           # Utility functions
│   ├── server.js            # Main server file
│   └── package.json         # Dependencies
└── README.md                # This file
```

## 🛠️ Technology Stack

### Backend
- **Node.js** - Runtime environment
- **Express.js** - Web framework
- **MySQL** - Database
- **bcryptjs** - Password hashing
- **nodemailer** - Email sending
- **express-session** - Session management

### Frontend
- **HTML5** - Structure
- **CSS3** - Styling
- **Vanilla JavaScript** - Client-side logic
- **Fetch API** - HTTP requests

## 📋 Prerequisites

- **Node.js** (v14 or higher)
- **MySQL** (v5.7 or higher) or **XAMPP** with MySQL
- **Gmail account** (for email notifications)
- **npm** or **yarn** (package manager)

## 🔧 Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd GSphere
```

### 2. Install Dependencies

**Option A: From root directory (recommended)**
```bash
npm install
```

**Option B: From nodejs directory**
```bash
cd GSphere
npm install
```

### 3. Database Configuration

#### Option A: Using XAMPP (Legacy PHP Setup)
1. Start XAMPP and ensure Apache and MySQL are running
2. Access phpMyAdmin at `http://localhost/phpmyadmin`
3. Create database `gpsphere_db` or run the initialization script

#### Option B: Using Node.js (Recommended)
1. Configure database connection in `.env` file (see below)
2. Run the database initialization script:
   ```bash
   # From root directory
   npm run init-db
   
   # Or from GSphere directory
   cd GSphere
   node scripts/initDb.js
   ```

### 4. Environment Variables

Create a `.env` file in the `nodejs/` directory:

```env
# Database Configuration
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=your_password
DB_NAME=gpsphere_db
DB_PORT=3306

# Email Configuration (Gmail)
EMAIL_USER=your_email@gmail.com
EMAIL_PASSWORD=your_app_password

# TAC Configuration
TAC_TEST_MODE=false

# Session Secret
SESSION_SECRET=your_secret_key_here
```

**Important Notes:**
- If using XAMPP with non-default MySQL port, update `DB_PORT` (e.g., `3307`)
- For Gmail, you need to generate an **App Password** (see Email Setup below)

### 5. Gmail App Password Setup

To enable email notifications, you need a Gmail App Password:

1. **Enable 2-Step Verification**
   - Go to [Google Account Security](https://myaccount.google.com/)
   - Enable 2-Step Verification

2. **Generate App Password**
   - Go to [App Passwords](https://myaccount.google.com/apppasswords)
   - Select "Mail" and "Other (Custom name)" → Enter "GPSphere"
   - Copy the 16-character password (ignore spaces)
   - Use this password in your `.env` file as `EMAIL_PASSWORD`

### 6. Start the Server

**From root directory:**
```bash
# Development mode (with auto-reload)
npm run dev

# Production mode
npm start
```

**Or from GSphere directory:**
```bash
cd GSphere
npm run dev  # Development mode
npm start    # Production mode
```

The server will start on `http://localhost:3000`

## 🎯 Usage

### Accessing the Application

- **Homepage**: `http://localhost:3000/homepage.html`
- **Login/Register**: `http://localhost:3000/login_register.html`
- **Admin Dashboard**: `http://localhost:3000/admin_dashboard.html` (Admin only)
- **Member Dashboard**: `http://localhost:3000/member_dashboard.html` (Members)
- **Student Dashboard**: `http://localhost:3000/student_dashboard.html` (Students)

### Default Admin Account

After database initialization:
- **Email**: `admin@gpsphere.com`
- **Password**: `Admin123!`

⚠️ **Change these credentials in production!**

## 📡 API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login (sends TAC)
- `POST /api/auth/verify-tac` - Verify TAC code
- `POST /api/auth/forgot-password` - Request password reset code
- `POST /api/auth/reset-password` - Reset password with code
- `POST /api/auth/logout` - Logout

### User Management
- `GET /api/user/profile` - Get user profile
- `GET /api/user/all` - Get all users (admin only)
- `POST /api/user/approve` - Approve user (admin only)

### Event Management
- `GET /api/events` - Get all events
- `GET /api/events/:id` - Get event details
- `POST /api/events` - Create event (admin only)
- `PUT /api/events/:id` - Update event (admin only)
- `DELETE /api/events/:id` - Delete event (admin only)

### Chatbot
- `POST /api/chatbot` - Send message to chatbot

## 🔐 Security Features

- **Password Requirements**: 8+ characters, uppercase, lowercase, number, symbol
- **Bcrypt Hashing**: Secure password storage
- **TAC 2FA**: Time-based authentication codes
- **Session Management**: Secure session handling
- **Role-Based Access Control**: Admin, Member, Student permissions
- **SQL Injection Protection**: Parameterized queries

## 🤖 Chatbot Features

The AI chatbot assistant provides:
- Real-time conversation with message history
- Context-aware responses
- Database integration for live event data
- Suggestion buttons for quick interactions
- Support for multiple topics:
  - GPS UTM information
  - Registration process
  - Login and TAC system
  - Events and activities
  - Contact information

See `nodejs/docs/chatbot-features.md` for detailed documentation.

## 📚 Documentation

Comprehensive documentation is available in the `nodejs/docs/` directory:

- `architecture.md` - System architecture overview
- `api-testing-guide.sh` - API testing instructions
- `setup/email-setup.md` - Email configuration guide
- `chatbot-features.md` - Chatbot feature documentation
- `migration/migration-guide.md` - Migration from PHP to Node.js
- `setup/step-by-step-guide.md` - Detailed setup instructions

## 🧪 Testing

```bash
# Run tests
npm test

# Run with coverage
npm run test:coverage
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 Development Notes

### TAC Test Mode

For local development, TAC codes appear on screen instead of being sent via email. To enable email mode:

1. Set `TAC_TEST_MODE=false` in `.env`
2. Configure Gmail App Password (see Email Setup)
3. Restart the server

### Database Port Configuration

If your MySQL uses a non-default port (e.g., 3307), update the `DB_PORT` in `.env` file.

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check MySQL is running
   - Verify credentials in `.env`
   - Ensure database exists

2. **Email Not Sending**
   - Verify Gmail App Password is correct
   - Check 2-Step Verification is enabled
   - Ensure `TAC_TEST_MODE=false` in production

3. **Port Already in Use**
   - Change port in `server.js` or use environment variable
   - Kill process using the port

## 📄 License

This project is developed for GPS UTM (Universiti Teknologi Malaysia).

## 👥 Team

- **Development Team**: ByteWave Application Development
- **Organization**: GPS UTM - Student Consumer Movement

## 🙏 Acknowledgments

- GPS UTM for the opportunity to develop this platform
- All contributors and team members

---

**GPS UTM** - Empowering students to become smart, ethical, and responsible consumers.

For more information, visit the [GPS UTM documentation](nodejs/docs/).
