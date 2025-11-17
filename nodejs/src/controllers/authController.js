// ============================================
// 📋 STEP 5: AUTHENTICATION CONTROLLER
// ============================================
// This replaces login.php and register.php
// Contains all authentication logic

const pool = require('../config/database');
const bcrypt = require('bcryptjs');
const { sendTACEmail, sendWelcomeEmail } = require('../utils/email');
require('dotenv').config();

// ========== REGISTER USER ==========
const register = async (req, res) => {
  try {
    const { name, email, password, confirm } = req.body;

    // Validation
    if (!name || !email || !password || !confirm) {
      return res.status(400).json({ error: 'All fields are required' });
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return res.status(400).json({ error: 'Invalid email format' });
    }

    // Password match check
    if (password !== confirm) {
      return res.status(400).json({ error: 'Passwords do not match' });
    }

    // Password strength validation
    const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (!passwordRegex.test(password)) {
      return res.status(400).json({ 
        error: 'Password must be at least 8 characters and include uppercase, lowercase, number, and symbol' 
      });
    }

    // Get connection from pool
    const conn = await pool.getConnection();

    // Check if email already exists
    const [rows] = await conn.query('SELECT id FROM users WHERE email = ?', [email]);
    if (rows.length > 0) {
      conn.release();
      return res.status(400).json({ error: 'Email already registered' });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);

    // Insert new user
    await conn.query(
      'INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)',
      [name, email, hashedPassword, 'student', 'pending']
    );

    conn.release();

    // Send welcome email
    await sendWelcomeEmail(email, name);

    return res.status(201).json({ 
      message: 'Registration successful! Your account is pending admin approval.' 
    });
  } catch (error) {
    console.error('Registration error:', error);
    return res.status(500).json({ error: 'Registration failed' });
  }
};

// ========== LOGIN - STEP 1: VERIFY EMAIL & PASSWORD ==========
const login = async (req, res) => {
  try {
    console.log('🔐 LOGIN REQUEST RECEIVED:', { body: req.body, path: req.path, method: req.method });
    const { email, password } = req.body;

    if (!email || !password) {
      return res.status(400).json({ error: 'Email and password required' });
    }

    const conn = await pool.getConnection();

    // Find user by email
    const [users] = await conn.query('SELECT * FROM users WHERE email = ?', [email]);

    if (users.length === 0) {
      conn.release();
      return res.status(400).json({ error: 'Invalid email or password' });
    }

    const user = users[0];

    // Verify password
    const isPasswordValid = await bcrypt.compare(password, user.password);
    if (!isPasswordValid) {
      conn.release();
      return res.status(400).json({ error: 'Invalid email or password' });
    }

    // Check if account is approved (TEMPORARILY DISABLED FOR TESTING)
    // if (user.status !== 'approved') {
    //   conn.release();
    //   return res.status(403).json({ 
    //     error: 'Your account is pending admin approval. Please wait.' 
    //   });
    // }

    // Generate 6-digit TAC code
    const tacCode = Math.floor(100000 + Math.random() * 900000);
    const tacExpiry = new Date(Date.now() + 15 * 60 * 1000); // 15 minutes from now

    // Save TAC in database
    await conn.query(
      'UPDATE users SET tac_code = ?, tac_expiry = ? WHERE id = ?',
      [tacCode, tacExpiry, user.id]
    );

    conn.release();

    // Send TAC via email
    await sendTACEmail(email, tacCode);

    // Return message (don't send token yet - user must verify TAC)
    return res.json({ 
      message: 'TAC code sent to your email. Please verify to complete login.',
      requiresTAC: true
    });
  } catch (error) {
    console.error('Login error:', error);
    return res.status(500).json({ error: 'Login failed' });
  }
};

// ========== VERIFY TAC - STEP 2: COMPLETE LOGIN ==========
const verifyTAC = async (req, res) => {
  try {
    const { email, tac_code } = req.body;

    if (!email || !tac_code) {
      return res.status(400).json({ error: 'Email and TAC code required' });
    }

    const conn = await pool.getConnection();

    // Find user and verify TAC
    const [users] = await conn.query(
      'SELECT * FROM users WHERE email = ? AND tac_code = ?',
      [email, tac_code]
    );

    if (users.length === 0) {
      conn.release();
      return res.status(400).json({ error: 'Invalid TAC code' });
    }

    const user = users[0];

    // Check if TAC expired
    if (new Date() > new Date(user.tac_expiry)) {
      conn.release();
      return res.status(400).json({ error: 'TAC code expired. Please login again.' });
    }

    // TAC verified - clear TAC
    await conn.query(
      'UPDATE users SET tac_code = NULL, tac_expiry = NULL WHERE id = ?',
      [user.id]
    );

    conn.release();

    // Store user in session (like PHP: $_SESSION['userId'] = user.id)
    req.session.userId = user.id;
    req.session.email = user.email;
    req.session.role = user.role;
    req.session.name = user.name;

    return res.json({ 
      message: 'Login successful',
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role
      }
    });
  } catch (error) {
    console.error('TAC verification error:', error);
    return res.status(500).json({ error: 'TAC verification failed' });
  }
};

module.exports = {
  register,
  login,
  verifyTAC
};
