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
  cancelApplication,
  updateApplicationStatus,
  submitFeedback,
  getEventFeedback
} = require('../controllers/eventController');

const router = express.Router();
// GET /events - Get all events
router.get('/', getAllEvents);

// GET /events/:eventId - Get single event
router.get('/:eventId', getEventById);

// POST /events - Create event (admin)
router.post('/', verifySession, checkRole(['admin']), createEvent);

// PUT /events/:eventId - Update event (admin)
router.put('/:eventId', verifySession, checkRole(['admin']), updateEvent);

// DELETE /events/:eventId - Delete event (admin)
router.delete('/:eventId', verifySession, checkRole(['admin']), deleteEvent);

// GET /events/:eventId/applications
router.get('/:eventId/applications', verifySession, checkRole(['admin']), getApplicationsForEvent);

// POST /events/applications/:applicationId/approve
router.post('/applications/:applicationId/approve', verifySession, checkRole(['admin']), approveApplication);

// POST /events/applications/:applicationId/reject
router.post('/applications/:applicationId/reject', verifySession, checkRole(['admin']), rejectApplication);

// PUT /events/applications/:applicationId
router.put('/applications/:applicationId', verifySession, checkRole(['admin']), updateApplicationStatus);

// POST /events/:eventId/apply
router.post('/:eventId/apply', verifySession, checkRole(['member']), applyForRole);

// DELETE /events/applications/:applicationId
router.delete('/applications/:applicationId', verifySession, checkRole(['member']), cancelApplication);

// POST /events/:eventId/feedback
router.post('/:eventId/feedback', verifySession, checkRole(['member']), submitFeedback);

// GET /events/:eventId/feedback
router.get('/:eventId/feedback', verifySession, checkRole(['admin']), getEventFeedback);

module.exports = router;
