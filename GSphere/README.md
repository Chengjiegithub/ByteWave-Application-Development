# GPS UTM Event Management System

A modern Node.js-based event management system for GPS UTM (Gerakan Pengguna Siswa - Student Consumer Movement).

## 🚀 Features

- **User Authentication**: TAC-based two-factor authentication via email
- **Role-Based Access**: Admin, Member, and Student dashboards
- **Event Management**: Create, update, and manage events
- **Email Notifications**: Automated welcome and verification emails
- **Modern UI**: Clean, responsive design with GPS UTM branding
- **Security**: Bcrypt password hashing, session management

## 📁 Project Structure

```
nodejs/
├── docs/                    # Documentation files
├── public/                  # Frontend files
│   ├── css/                # Stylesheets
│   ├── images/             # Logo and images
│   ├── js/                 # Client-side JavaScript
│   ├── admin_dashboard.html
│   ├── member_dashboard.html
│   ├── student_dashboard.html
│   ├── login_register.html
│   └── homepage.html
├── scripts/                # Utility scripts
│   └── initDb.js          # Database initialization
├── src/                    # Backend source code
│   ├── config/            # Configuration files
│   ├── controllers/       # Route controllers
│   ├── middleware/        # Express middleware
│   ├── routes/            # API routes
│   └── utils/             # Utility functions
├── .env                    # Environment variables
├── server.js              # Main server file
└── package.json           # Dependencies

```

## 🛠️ Setup Instructions

### Prerequisites
- Node.js (v14+)
- MySQL (v5.7+)
- Gmail account for email notifications

### Installation

1. **Install dependencies:**
   ```bash
   cd nodejs
   npm install
   ```

2. **Configure environment variables:**
   Create `.env` file:
   ```env
   DB_HOST=localhost
   DB_USER=root
   DB_PASSWORD=your_password
   DB_NAME=gpsphere_db
   DB_PORT=3306
   
   EMAIL_USER=your_email@gmail.com
   EMAIL_PASSWORD=your_app_password
   
   TAC_TEST_MODE=false
   ```

3. **Initialize database:**
   ```bash
   node scripts/initDb.js
   ```

4. **Start the server:**
   ```bash
   node server.js
   ```

5. **Access the application:**
   - Homepage: http://localhost:3000/homepage.html
   - Login: http://localhost:3000/login_register.html

## 🔑 Default Admin Account

- **Email:** admin@gpsphere.com
- **Password:** Admin123!

## 📚 API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login (sends TAC)
- `POST /api/auth/verify-tac` - Verify TAC code

### User Management
- `GET /api/user/profile` - Get user profile
- `GET /api/user/all` - Get all users (admin)
- `POST /api/user/approve` - Approve user (admin)

### Event Management
- `GET /api/events` - Get all events
- `GET /api/events/:id` - Get event details
- `POST /api/events` - Create event (admin)
- `PUT /api/events/:id` - Update event (admin)
- `DELETE /api/events/:id` - Delete event (admin)

### Chatbot
- `POST /api/chatbot` - Send message to chatbot

## 🎨 User Roles

1. **Admin** - Full system access, user approval, event management
2. **Member** - Event participation, profile management
3. **Student** - Pending approval status

## 📖 Documentation

See the `docs/` folder for detailed documentation:
- Architecture overview
- API testing guide
- Email setup instructions
- Migration guide from PHP
- UX enhancements

## 🔒 Security Features

- Password requirements: 8+ characters, uppercase, lowercase, number, symbol
- Bcrypt password hashing
- TAC (Time-based Access Code) 2FA
- Email verification
- Session management
- Role-based access control

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## 📝 License

This project is developed for GPS UTM (Universiti Teknologi Malaysia).

## 👥 Team

- Development Team: ByteWave Application Development
- Organization: GPS UTM - Student Consumer Movement

---

**GPS UTM** - Empowering students to become smart, ethical, and responsible consumers.
