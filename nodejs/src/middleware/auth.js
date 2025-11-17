// ============================================
// 📋 SESSION-BASED AUTHENTICATION
// ============================================
// Like PHP: req.session['user_id'] = user.id
// Session stored server-side, cookie sent to browser

// Middleware to verify session
const verifySession = (req, res, next) => {
  if (!req.session || !req.session.userId) {
    return res.status(401).json({ error: 'Not logged in' });
  }
  req.userId = req.session.userId;
  req.userRole = req.session.role;
  next();
};

// Middleware to check user role (for admin, member access)
const checkRole = (requiredRoles) => {
  return (req, res, next) => {
    if (!req.session || !req.session.userId) {
      return res.status(401).json({ error: 'Not logged in' });
    }

    if (!requiredRoles.includes(req.session.role)) {
      return res.status(403).json({ error: 'Access denied - insufficient permissions' });
    }

    next();
  };
};

module.exports = {
  verifySession,
  checkRole
};
