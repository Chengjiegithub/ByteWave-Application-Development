// ============================================
// 📋 STEP 9: EVENTS CONTROLLER
// ============================================
// Replaces event management from your PHP files

const pool = require('../config/database');

const getAllEvents = async (req, res) => {
  try {
    const conn = await pool.getConnection();

    // 1. Fetch events
    const [events] = await conn.query("SELECT * FROM events ORDER BY event_date DESC");

    // 2. Fetch roles + counts for each event
    for (const event of events) {
      const [roles] = await conn.query(
        "SELECT id AS role_id, role_name, slots FROM event_roles WHERE event_id = ?",
        [event.id]
      );

      for (const r of roles) {
        const [approved] = await conn.query(
          'SELECT COUNT(*) AS cnt FROM event_applications WHERE role_id = ? AND status = "approved"',
          [r.role_id]
        );
        const [pending] = await conn.query(
          'SELECT COUNT(*) AS cnt FROM event_applications WHERE role_id = ? AND status = "pending"',
          [r.role_id]
        );

        r.approvedCount = approved[0].cnt || 0;
        r.pendingCount = pending[0].cnt || 0;
      }

      event.roles = roles;
    }

    conn.release();
    return res.json(events);

  } catch (error) {
    console.error("Error fetching events:", error);
    return res.status(500).json({ error: "Failed to fetch events" });
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
    const { event_name, description, event_date, event_time, location, roles } = req.body;

    if (!event_name || !event_date) {
      return res.status(400).json({ error: 'Event name and date required' });
    }

    const conn = await pool.getConnection();

    // Get creator from session
    const createdBy = req.session?.email || req.session?.userId;

    // 1. Insert event basic info
    const [result] = await conn.query(
      `INSERT INTO events 
       (event_name, description, event_date, event_time, location, created_by, status)
       VALUES (?, ?, ?, ?, ?, ?, 'ongoing')`,
      [event_name, description, event_date, event_time, location, createdBy]
    );

    const eventId = result.insertId;

    // 2. Insert roles into event_roles
    if (Array.isArray(roles)) {
      for (const r of roles) {
        await conn.query(
          `INSERT INTO event_roles (event_id, role_name, slots)
           VALUES (?, ?, ?)`,
          [eventId, r.role_name, r.total_needed]
        );
      }
    }

    conn.release();

    return res.status(201).json({
      message: 'Event created with custom roles!',
      eventId
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

// Member apply for event role
const applyForRole = async (req, res) => {
  try {
    const eventId = parseInt(req.params.id, 10);
    const { role_id } = req.body;
    const userId = req.session?.userId;

    if (!userId) return res.status(401).json({ error: 'Not logged in' });
    if (!role_id) return res.status(400).json({ error: 'role_id required' });

    const conn = await pool.getConnection();

    // Ensure approved member
    const [users] = await conn.query('SELECT role, status FROM users WHERE id = ?', [userId]);
    const u = users[0];
    if (!u || u.role !== 'member' || u.status !== 'approved') {
      conn.release();
      return res.status(403).json({ error: 'Only approved members can apply' });
    }

    // Ensure role belongs to event
    const [roles] = await conn.query(
      'SELECT id, slots FROM event_roles WHERE id = ? AND event_id = ?',
      [role_id, eventId]
    );
    if (roles.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Role not found in event' });
    }

    // Check if already applied
    const [existing] = await conn.query(
      'SELECT id FROM event_applications WHERE event_id = ? AND user_id = ? AND status IN ("pending","approved")',
      [eventId, userId]
    );
    if (existing.length > 0) {
      conn.release();
      return res.status(400).json({ error: 'Already applied for this event' });
    }

    // Create application
    await conn.query(
      'INSERT INTO event_applications (event_id, role_id, user_id, status) VALUES (?, ?, ?, "pending")',
      [eventId, role_id, userId]
    );

    conn.release();
    return res.status(201).json({ message: 'Application submitted' });

    } catch (err) {
      console.error('applyForRole error:', err);
      return res.status(500).json({ error: 'Failed to apply for role' });
    }
};

//Member cancel for pending role application
const cancelApplication = async (req, res) => {
  try {
    const applicationId = parseInt(req.params.id, 10);
    const userId = req.session?.userId;
    if (!userId) return res.status(401).json({ error: 'Not logged in' });

    const conn = await pool.getConnection();

    // Verify application exists, belongs to user and is pending
    const [rows] = await conn.query(
      'SELECT id, user_id, status FROM event_applications WHERE id = ?',
      [applicationId]
    );

    if (rows.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Application not found' });
    }

    const app = rows[0];
    if (app.user_id !== userId) {
      conn.release();
      return res.status(403).json({ error: 'Not allowed to cancel this application' });
    }

    if (app.status !== 'pending') {
      conn.release();
      return res.status(400).json({ error: 'Only pending applications can be cancelled' });
    }

    // Mark as rejected (acts as cancelled)
    await conn.query('UPDATE event_applications SET status = "rejected" WHERE id = ?', [applicationId]);

    conn.release();
    return res.json({ message: 'Application cancelled' });
  } catch (err) {
    console.error('cancelApplication error:', err);
    return res.status(500).json({ error: 'Failed to cancel application' });
  }
};


// Admin view applications for event roles
const getApplicationsForEvent = async (req, res) => {
  try {
    const eventId = parseInt(req.params.id, 10);
    const conn = await pool.getConnection();

    // 1. Fetch roles for the event
    const [roles] = await conn.query(
      "SELECT id AS role_id, role_name, slots FROM event_roles WHERE event_id = ?",
      [eventId]
    );

    const results = [];

    // 2. For each role, fetch applications + approved count
    for (const r of roles) {
      const [apps] = await conn.query(
        `SELECT ea.id, ea.status, ea.created_at,
                u.name, u.email, u.id AS user_id
         FROM event_applications ea
         JOIN users u ON ea.user_id = u.id
         WHERE ea.role_id = ?
         ORDER BY ea.created_at ASC`,
        [r.role_id]
      );

      const [approvedCount] = await conn.query(
        'SELECT COUNT(*) AS cnt FROM event_applications WHERE role_id = ? AND status="approved"',
        [r.role_id]
      );

      results.push({
        role_id: r.role_id,
        role_name: r.role_name,
        slots: r.slots,
        approvedCount: approvedCount[0].cnt || 0,
        applications: apps
      });
    }

    conn.release();
    return res.json({ eventId, roles: results });

  } catch (err) {
    console.error('getApplicationsForEvent error:', err);
    return res.status(500).json({ error: 'Failed to fetch applications' });
  }
};


//Admin approve for event role application
const approveApplication = async (req, res) => {
  try {
    const applicationId = parseInt(req.params.id, 10);
    const conn = await pool.getConnection();

    const [apps] = await conn.query(
      `SELECT ea.role_id, r.slots
       FROM event_applications ea
       JOIN event_roles r ON ea.role_id = r.id
       WHERE ea.id = ?`,
      [applicationId]
    );

    if (apps.length === 0) {
      conn.release();
      return res.status(404).json({ error: 'Application not found' });
    }

    const { role_id, slots } = apps[0];

    // Count approved
    const [count] = await conn.query(
      'SELECT COUNT(*) AS cnt FROM event_applications WHERE role_id = ? AND status="approved"',
      [role_id]
    );

    if (count[0].cnt >= slots) {
      conn.release();
      return res.status(400).json({ error: 'No slots available' });
    }

    // Approve
    await conn.query('UPDATE event_applications SET status="approved" WHERE id=?', [applicationId]);

    conn.release();
    return res.json({ message: 'Application approved' });

  } catch (err) {
    console.error('approveApplication error:', err);
    return res.status(500).json({ error: 'Failed to approve' });
  }
};

//Admin reject for event role application
const rejectApplication = async (req, res) => {
  try {
    const applicationId = parseInt(req.params.id, 10);
    const conn = await pool.getConnection();

    await conn.query('UPDATE event_applications SET status="rejected" WHERE id=?', [applicationId]);

    conn.release();
    return res.json({ message: 'Application rejected' });

  } catch (err) {
    console.error('rejectApplication error:', err);
    return res.status(500).json({ error: 'Failed to reject application' });
  }
};


module.exports = {
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
};
