// ============================================
// 📋 STEP 9: EVENTS CONTROLLER
// ============================================
// Replaces event management from your PHP files

const pool = require('../config/database');

// Get all events
const getAllEvents = async (req, res) => {
  try {
    const conn = await pool.getConnection();

    const [events] = await conn.query(
      'SELECT * FROM events ORDER BY event_date DESC'
    );

    conn.release();

    return res.json(events);
  } catch (error) {
    console.error('Error fetching events:', error);
    return res.status(500).json({ error: 'Failed to fetch events' });
  }
};

// Get single event
const getEventById = async (req, res) => {
  try {
    const { eventId } = req.params;
    const conn = await pool.getConnection();

    const [events] = await conn.query(
      'SELECT * FROM events WHERE id = ?',
      [eventId]
    );

    if (events.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Event not found' });
    }

    // Get event roles
    const [roles] = await conn.query(
      'SELECT * FROM event_roles WHERE event_id = ?',
      [eventId]
    );

    conn.release();

    return res.json({ ...events[0], roles });
  } catch (error) {
    console.error('Error fetching event:', error);
    return res.status(500).json({ error: 'Failed to fetch event' });
  }
};

// Create event (admin only)
const createEvent = async (req, res) => {
  try {
    const { event_name, description, event_date, event_time, location, director_needed, helper_needed } = req.body;

    if (!event_name || !event_date) {
      return res.status(400).json({ error: 'Event name and date required' });
    }

    const conn = await pool.getConnection();

    const [result] = await conn.query(
      'INSERT INTO events (event_name, description, event_date, event_time, location, director_needed, helper_needed, created_by, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
      [event_name, description, event_date, event_time, location, director_needed, helper_needed, req.user.email, 'ongoing']
    );

    conn.release();

    return res.status(201).json({ 
      message: 'Event created successfully',
      eventId: result.insertId 
    });
  } catch (error) {
    console.error('Error creating event:', error);
    return res.status(500).json({ error: 'Failed to create event' });
  }
};

// Update event (admin only)
const updateEvent = async (req, res) => {
  try {
    const { eventId } = req.params;
    const { event_name, description, event_date, event_time, location, status } = req.body;

    const conn = await pool.getConnection();

    await conn.query(
      'UPDATE events SET event_name = ?, description = ?, event_date = ?, event_time = ?, location = ?, status = ? WHERE id = ?',
      [event_name, description, event_date, event_time, location, status, eventId]
    );

    conn.release();

    return res.json({ message: 'Event updated successfully' });
  } catch (error) {
    console.error('Error updating event:', error);
    return res.status(500).json({ error: 'Failed to update event' });
  }
};

// Delete event (admin only)
const deleteEvent = async (req, res) => {
  try {
    const { eventId } = req.params;
    const conn = await pool.getConnection();

    await conn.query('DELETE FROM events WHERE id = ?', [eventId]);

    conn.release();

    return res.json({ message: 'Event deleted successfully' });
  } catch (error) {
    console.error('Error deleting event:', error);
    return res.status(500).json({ error: 'Failed to delete event' });
  }
};

module.exports = {
  getAllEvents,
  getEventById,
  createEvent,
  updateEvent,
  deleteEvent
};
