// ============================================
// 📋 STEP 11: MAIN SERVER FILE
// ============================================
// This is your application entry point (like index.php in PHP)
// Starts Express server and sets up all routes

const express = require('express');
const session = require('express-session');
const cors = require('cors');
require('dotenv').config();

const app = express();

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Session middleware (like PHP: session_start())
app.use(session({
  secret: process.env.JWT_SECRET || 'your-secret-key',
  resave: false,
  saveUninitialized: false,
  cookie: { maxAge: 24 * 60 * 60 * 1000 } // 24 hours
}));

// Serve static files (CSS, JS)
app.use(express.static('public'));

// Import routes
const authRoutes = require('./src/routes/authRoutes');
const userRoutes = require('./src/routes/userRoutes');
const eventRoutes = require('./src/routes/eventRoutes');
const chatbotRoutes = require('./src/routes/chatbotRoutes');

// Use routes
app.use('/api/auth', authRoutes);
app.use('/api/user', userRoutes);
app.use('/api/events', eventRoutes);
app.use('/api/chatbot', chatbotRoutes);

// Health check endpoint
app.get('/api/health', (req, res) => {
  res.json({ status: 'Server is running ✅' });
});

// 404 handler
app.use((req, res) => {
  res.status(404).json({ error: 'Endpoint not found' });
});

// Error handler
app.use((error, req, res, next) => {
  console.error('Error:', error);
  res.status(500).json({ error: 'Internal server error' });
});

// Start server
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`🚀 Server running on http://localhost:${PORT}`);
  console.log('📚 API Documentation:');
  console.log(`   POST   /api/auth/register      - Register new user`);
  console.log(`   POST   /api/auth/login         - Login user (sends TAC)`);
  console.log(`   POST   /api/auth/verify-tac    - Verify TAC and get token`);
  console.log(`   GET    /api/user/profile       - Get user profile`);
  console.log(`   GET    /api/user/all           - Get all users (admin)`);
  console.log(`   POST   /api/user/approve       - Approve user (admin)`);
  console.log(`   GET    /api/events             - Get all events`);
  console.log(`   GET    /api/events/:id         - Get event details`);
  console.log(`   POST   /api/events             - Create event (admin)`);
  console.log(`   PUT    /api/events/:id         - Update event (admin)`);
  console.log(`   DELETE /api/events/:id         - Delete event (admin)`);
  console.log(`   POST   /api/chatbot            - Send message to chatbot`);
});
