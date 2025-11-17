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
      password: process.env.DB_PASSWORD || '',
      port: process.env.DB_PORT || 3306
    });

    console.log('✅ Connected to MySQL');

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
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('✅ Table users created or exists');

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

    // 6. Insert default admin if not exists
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
