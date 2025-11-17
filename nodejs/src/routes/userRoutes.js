// ============================================
// 📋 STEP 8: USER ROUTES
// ============================================

const express = require('express');
const { verifySession, checkRole } = require('../middleware/auth');
const { 
  getUserProfile, 
  getAllUsers, 
  approveUser 
} = require('../controllers/userController');

const router = express.Router();

// GET /user/profile - Get logged-in user profile
router.get('/profile', verifySession, getUserProfile);

// GET /user/all - Get all users (admin only)
router.get('/all', verifySession, checkRole(['admin']), getAllUsers);

// POST /user/approve - Approve user (admin only)
router.post('/approve', verifySession, checkRole(['admin']), approveUser);

module.exports = router;
