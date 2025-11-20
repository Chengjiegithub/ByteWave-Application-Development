// ============================================
// 📋 STEP 6: AUTHENTICATION ROUTES
// ============================================
// This replaces the PHP form submissions
// Defines API endpoints for login/register/verify

const express = require('express');
const { register, login, verifyTAC, logout } = require('../controllers/authController');

const router = express.Router();

// POST /auth/register - Register new user
router.post('/register', register);

// POST /auth/login - Login user (sends TAC)
router.post('/login', login);

// POST /auth/verify-tac - Verify TAC and get JWT token
router.post('/verify-tac', verifyTAC);

// POST /auth/logout - Logout user
router.post('/logout', logout);

module.exports = router;
