<?php
$host = "localhost";
$user = "root";
$pass = "";
$port = 3307; // ⚠️ Change this if your MySQL uses another port

// 1️⃣ Connect to MySQL Server (no database selected yet)
$conn = new mysqli($host, $user, $pass, "", $port);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// 2️⃣ Create database
$dbname = "gpsphere_db";
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "✅ Database '$dbname' created or already exists.<br>";
} else {
    die("❌ Error creating database: " . $conn->error);
}

// 3️⃣ Select the database
$conn->select_db($dbname);

// 4️⃣ Create `users` table
$table = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','member','admin') DEFAULT 'student',
    status ENUM('pending','approved') DEFAULT 'pending',
    tac_code VARCHAR(10),
    tac_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($table) === TRUE) {
    echo "✅ Table 'users' created or already exists.<br>";
} else {
    die("❌ Error creating table: " . $conn->error);
}

// 5️⃣ Optional: Add an admin account
$adminEmail = "admin@gpsphere.com";
$adminPass = password_hash("Admin123!", PASSWORD_DEFAULT);
$checkAdmin = $conn->query("SELECT * FROM users WHERE email='$adminEmail'");
if ($checkAdmin->num_rows == 0) {
    $insertAdmin = "INSERT INTO users (name, email, password, role, status) 
                    VALUES ('System Admin', '$adminEmail', '$adminPass', 'admin', 'approved')";
    if ($conn->query($insertAdmin)) {
        echo "✅ Default admin account created (Email: $adminEmail | Password: Admin123!)<br>";
    } else {
        echo "⚠️ Admin insert failed: " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Admin account already exists.<br>";
}

// 6️⃣ Create Events Table
$events = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE,
    event_time TIME,
    location VARCHAR(150),
    director_needed INT DEFAULT 1,
    helper_needed INT DEFAULT 5,
    created_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($events) === TRUE) {
    echo "✅ Table 'events' created or already exists.<br>";
} else {
    echo "❌ Error creating events table: " . $conn->error . "<br>";
}

// ✅ Check if 'status' column already exists before adding
$checkColumn = $conn->query("SHOW COLUMNS FROM events LIKE 'status'");
if ($checkColumn->num_rows == 0) {
    $conn->query("ALTER TABLE events ADD COLUMN status ENUM('ongoing','finished') DEFAULT 'ongoing'");
    echo "✅ Added 'status' column to 'events' table.<br>";
} else {
    echo "ℹ️ 'status' column already exists in 'events' table.<br>";
}

// 7️⃣ Create Event Roles Table
$event_roles = "CREATE TABLE IF NOT EXISTS event_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    role_name VARCHAR(100) NOT NULL,
    slots INT DEFAULT 1,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
)";
if ($conn->query($event_roles) === TRUE) {
    echo "✅ Table 'event_roles' created or already exists.<br>";
} else {
    echo "❌ Error creating event_roles table: " . $conn->error . "<br>";
}

// 8️⃣ Create Event Requests Table
$event_requests = "CREATE TABLE IF NOT EXISTS event_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES event_roles(id) ON DELETE CASCADE
)";
if ($conn->query($event_requests) === TRUE) {
    echo "✅ Table 'event_requests' created or already exists.<br>";
} else {
    echo "❌ Error creating event_requests table: " . $conn->error . "<br>";
}

// 9️⃣ Create Notifications Table
$notifications = "CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
if ($conn->query($notifications) === TRUE) {
    echo "✅ Table 'notifications' created or already exists.<br>";
} else {
    echo "❌ Error creating notifications table: " . $conn->error . "<br>";
}

echo "<hr><b>🎉 Database setup completed successfully!</b>";
$conn->close();
?>
