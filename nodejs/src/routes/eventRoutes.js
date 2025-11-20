// ============================================
// 📋 STEP 10: EVENTS ROUTES
// ============================================

const express = require('express');
const { verifySession, checkRole } = require('../middleware/auth');
const { 
  getAllEvents,
  getEventById,
  createEvent,
  updateEvent,
  deleteEvent,
  applyForRole,
  getApplicationsForEvent,
  approveApplication,
  rejectApplication,
  cancelApplication
} = require('../controllers/eventController');

const router = express.Router();

// GET /events - Get all events (public)
router.get('/', getAllEvents);

// GET /events/:eventId - Get single event (public)
router.get('/:eventId', getEventById);

// POST /events - Create event (admin only)
router.post('/', verifySession, checkRole(['admin']), createEvent);

// PUT /events/:eventId - Update event (admin only)
router.put('/:eventId', verifySession, checkRole(['admin']), updateEvent);

// DELETE /events/:eventId - Delete event (admin only)
router.delete('/:eventId', verifySession, checkRole(['admin']), deleteEvent);

// GET /events/:id/applications - view all applications for an event (admin only)
router.get('/:id/applications', verifySession, checkRole(['admin']), getApplicationsForEvent);

// POST /events/applications/:id/approve - approve an application (admin only)
router.post('/applications/:id/approve', verifySession, checkRole(['admin']), approveApplication);

// POST /events/applications/:id/reject - reject an application (admin only)
router.post('/applications/:id/reject', verifySession, checkRole(['admin']), rejectApplication);

// POST /events/:id/apply - member applies for a role (member)
router.post('/:id/apply', verifySession, applyForRole);

// DELETE /events/applications/:id - member cancels their pending application
router.delete('/applications/:id', verifySession, cancelApplication);




module.exports = router;
