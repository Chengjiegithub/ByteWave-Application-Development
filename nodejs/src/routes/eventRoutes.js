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
  deleteEvent
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

module.exports = router;
