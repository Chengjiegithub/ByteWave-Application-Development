// ============================================
// 📋 STEP 12: DATABASE INITIALIZATION
// ============================================
// Replaces create_database.php
// Run this once to set up all tables

const mysql = require('mysql2/promise');
require('dotenv').config();

console.log('Starting database initialization...');
console.log('DB_HOST:', process.env.DB_HOST);
console.log('DB_USER:', process.env.DB_USER);

async function initializeDatabase() {
  let conn;
  try {
    console.log('Attempting MySQL connection...');
    // Connect to MySQL without selecting database
    conn = await mysql.createConnection({
      host: process.env.DB_HOST || 'localhost',
      user: process.env.DB_USER || 'root',
      password: process.env.DB_PASSWORD ||'',
      port: parseInt(process.env.DB_PORT || '3307')
    });

    console.log('✅ Connected to MySQL');
    console.log(`   Host: ${process.env.DB_HOST || 'localhost'}`);
    console.log(`   Port: ${process.env.DB_PORT || '3307'}`);
    console.log(`   User: ${process.env.DB_USER || 'root'}`);

    const dbName = process.env.DB_NAME || 'gpsphere_db';

    // 1. Create database
    await conn.query(`CREATE DATABASE IF NOT EXISTS ${dbName}`);
    console.log(`✅ Database '${dbName}' created or exists`);

    // 2. Select database
    await conn.query(`USE ${dbName}`);

    // 3. Create users table
    await conn.query(`
      CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('student','member','admin') DEFAULT 'student',
        status ENUM('pending','approved') DEFAULT 'pending',
        tac_code VARCHAR(10),
        tac_expiry DATETIME,
        reset_code VARCHAR(10),
        reset_expiry DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('✅ Table users created or exists');

    // Ensure reset columns exist (for older installations)
    await conn.query(`
      ALTER TABLE users
      ADD COLUMN IF NOT EXISTS reset_code VARCHAR(10),
      ADD COLUMN IF NOT EXISTS reset_expiry DATETIME
    `);
    console.log('✅ Reset columns ensured on users table');

    // Ensure profile_picture column exists
    await conn.query(`
      ALTER TABLE users
      ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) DEFAULT NULL
    `);
    console.log('✅ Profile picture column ensured on users table');

    // 4. Create events table
    await conn.query(`
      CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_name VARCHAR(200) NOT NULL,
        description TEXT,
        event_date DATE,
        event_time TIME,
        location VARCHAR(150),
        director_needed INT DEFAULT 1,
        helper_needed INT DEFAULT 5,
        status ENUM('ongoing','finished') DEFAULT 'ongoing',
        created_by VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('✅ Table events created or exists');

    // 5. Create event_roles table
    await conn.query(`
      CREATE TABLE IF NOT EXISTS event_roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        role_name VARCHAR(100) NOT NULL,
        slots INT DEFAULT 1,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
      )
    `);
    console.log('✅ Table event_roles created or exists');

    // 6. Create event_applications table
    await conn.query(`
      CREATE TABLE IF NOT EXISTS event_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        role_id INT NOT NULL,
        user_id INT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        FOREIGN KEY (role_id) REFERENCES event_roles(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
      )
    `);
    console.log('✅ Table event_applications created or exists');

     //7. Setup event_feedback table
    await conn.query(`
      CREATE TABLE IF NOT EXISTS event_feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
         event_id INT NOT NULL,
        user_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE (event_id, user_id)
      )
    `);
    console.log('✅ Table event_feedback created or exists');

    // 8. Create notifications table
    await conn.query(`
      CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        type VARCHAR(50) NOT NULL DEFAULT 'event',
        title VARCHAR(200) NOT NULL,
        message TEXT,
        related_id INT,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_read (user_id, is_read),
        INDEX idx_created_at (created_at)
      )
    `);
    console.log('✅ Table notifications created or exists');

    // 9. Insert default admin if not exists
    const [admins] = await conn.query(
      "SELECT * FROM users WHERE email = 'admin@gpsphere.com'"
    );

    if (admins.length === 0) {
      const bcrypt = require('bcryptjs');
      const hashedPassword = await bcrypt.hash('Admin123!', 10);
      
      await conn.query(
        "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)",
        ['System Admin', 'admin@gpsphere.com', hashedPassword, 'admin', 'approved']
      );
      console.log('✅ Default admin created (Email: admin@gpsphere.com | Password: Admin123!)');
    } else {
      console.log('ℹ️  Admin account already exists');
    }

    console.log('\n🎉 Database initialization complete!');
    process.exit(0);
  } catch (error) {
    console.error('❌ Error:', error);
    console.error('Error message:', error.message);
    console.error('Error code:', error.code);
    process.exit(1);
  } finally {
    if (conn) await conn.end();
  }

}

initializeDatabase();
