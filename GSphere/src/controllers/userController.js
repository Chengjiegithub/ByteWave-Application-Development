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
    "UPDATE users SET status = 'approved', role = 'member' WHERE id = ?",
    [userId]
  );

    conn.release();

    return res.json({ message: 'User approved successfully' });
  } catch (error) {
    console.error('Error approving user:', error);
    return res.status(500).json({ error: 'Failed to approve user' });
  }
};

// Update user status (admin only) - for approve/reject
const updateUserStatus = async (req, res) => {
  try {
    const userId = req.params.userId;
    const { status } = req.body;
    
    if (!['approved', 'rejected', 'pending'].includes(status)) {
      return res.status(400).json({ error: 'Invalid status' });
    }

    const conn = await pool.getConnection();

    // If approving, also set role to member
    if (status === 'approved') {
      await conn.query(
        "UPDATE users SET status = ?, role = 'member' WHERE id = ?",
        [status, userId]
      );
    } else {
      await conn.query(
        "UPDATE users SET status = ? WHERE id = ?",
        [status, userId]
      );
    }

    conn.release();

    return res.json({ message: `User ${status} successfully` });
  } catch (error) {
    console.error('Error updating user status:', error);
    return res.status(500).json({ error: 'Failed to update user status' });
  }
};

const getMyApplications = async (req, res) => {
  try {
    const userId = req.session?.userId;
    if (!userId) return res.status(401).json({ error: 'Not logged in' });

    const [rows] = await pool.query(
      `SELECT 
          ea.id,
          ea.event_id,
          ea.role_id,
          ea.status,
          ea.created_at,
          r.role_name,
          e.event_name
       FROM event_applications ea
       JOIN event_roles r ON ea.role_id = r.id
       JOIN events e ON ea.event_id = e.id
       WHERE ea.user_id = ?
       ORDER BY ea.created_at DESC`,
      [userId]
    );

    res.json(rows);
  } catch (err) {
    console.error("getMyApplications error:", err);
    res.status(500).json({ error: "Failed to fetch applications" });
  }
};

// Change user password
const changePassword = async (req, res) => {
  try {
    const userId = req.userId; // From verifySession middleware
    const { currentPassword, newPassword } = req.body;

    if (!currentPassword || !newPassword) {
      return res.status(400).json({ error: 'Current and new password required' });
    }

    // Password strength validation
    const bcrypt = require('bcryptjs');
    const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (!passwordRegex.test(newPassword)) {
      return res.status(400).json({ 
        error: 'Password must be at least 8 characters and include uppercase, lowercase, number, and symbol' 
      });
    }

    const conn = await pool.getConnection();

    // Get current user
    const [users] = await conn.query('SELECT password FROM users WHERE id = ?', [userId]);
    
    if (users.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'User not found' });
    }

    // Verify current password
    const isValid = await bcrypt.compare(currentPassword, users[0].password);
    if (!isValid) {
      conn.release();
      return res.status(400).json({ error: 'Current password is incorrect' });
    }

    // Hash new password
    const hashedPassword = await bcrypt.hash(newPassword, 10);

    // Update password
    await conn.query('UPDATE users SET password = ? WHERE id = ?', [hashedPassword, userId]);

    conn.release();

    return res.json({ message: 'Password changed successfully' });
  } catch (error) {
    console.error('Error changing password:', error);
    return res.status(500).json({ error: 'Failed to change password' });
  }
};


module.exports = {
  getUserProfile,
  getAllUsers,
  approveUser,
  updateUserStatus,
  getMyApplications,
  changePassword
};
