// ============================================
// 📋 STEP 7: USER CONTROLLER & ROUTES
// ============================================
// Handles user profile, dashboard, role-based access

const pool = require('../config/database');

// Get user profile
const getUserProfile = async (req, res) => {
  try {
    const userId = req.userId; // Set by verifySession middleware
    const conn = await pool.getConnection();

    const [users] = await conn.query(
      'SELECT id, name, email, role, status, created_at FROM users WHERE id = ?',
      [userId]
    );

    conn.release();

    if (users.length === 0) {
      return res.status(404).json({ error: 'User not found' });
    }

    return res.json(users[0]);
  } catch (error) {
    console.error('Error fetching profile:', error);
    return res.status(500).json({ error: 'Failed to fetch profile' });
  }
};

// Get all users (admin only)
const getAllUsers = async (req, res) => {
  try {
    const conn = await pool.getConnection();

    const [users] = await conn.query(
      'SELECT id, name, email, role, status, created_at FROM users'
    );

    conn.release();

    return res.json(users);
  } catch (error) {
    console.error('Error fetching users:', error);
    return res.status(500).json({ error: 'Failed to fetch users' });
  }
};

// Approve user account (admin only)
const approveUser = async (req, res) => {
  try {
    const { userId } = req.body;
    const conn = await pool.getConnection();

    await conn.query(
      'UPDATE users SET status = ? WHERE id = ?',
      ['approved', userId]
    );

    conn.release();

    return res.json({ message: 'User approved successfully' });
  } catch (error) {
    console.error('Error approving user:', error);
    return res.status(500).json({ error: 'Failed to approve user' });
  }
};

module.exports = {
  getUserProfile,
  getAllUsers,
  approveUser
};
