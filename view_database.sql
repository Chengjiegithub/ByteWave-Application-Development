-- ============================================
-- GPSphere Database Viewer
-- Run this in MySQL Workbench to see all data
-- ============================================

-- Select the database
USE gpsphere_db;

-- Show all tables
SHOW TABLES;

-- ============================================
-- VIEW ALL USERS
-- ============================================
SELECT 
    '========== USERS TABLE ==========' as '';

SELECT 
    id,
    name,
    email,
    role,
    status,
    created_at,
    tac_code,
    tac_expiry
FROM users
ORDER BY id;

-- ============================================
-- VIEW ALL EVENTS
-- ============================================
SELECT 
    '========== EVENTS TABLE ==========' as '';

SELECT * FROM events;

-- ============================================
-- VIEW ALL EVENT ROLES
-- ============================================
SELECT 
    '========== EVENT ROLES TABLE ==========' as '';

SELECT * FROM event_roles;

-- ============================================
-- SUMMARY STATISTICS
-- ============================================
SELECT 
    '========== DATABASE SUMMARY ==========' as '';

SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM users WHERE role = 'admin') as admins,
    (SELECT COUNT(*) FROM users WHERE role = 'member') as members,
    (SELECT COUNT(*) FROM users WHERE role = 'student') as students,
    (SELECT COUNT(*) FROM events) as total_events,
    (SELECT COUNT(*) FROM event_roles) as total_event_roles;
