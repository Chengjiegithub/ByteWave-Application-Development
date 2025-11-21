<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerakan Pengguna Siswa UTM | Home</title>
    <link rel="stylesheet" href="assests/css/base.css">
    <link rel="stylesheet" href="assests/css/index.css">
</head>
<body>
    <header class="header">
        <div class="header-top">
            <div class="logo-section">
                <img src="assests/Official GPS Logo Coloured.png" alt="GPS Logo" class="logo-img">
                <div class="header-text">
                    <div class="main-title">Gerakan Pengguna Siswa UTM</div>
                    <div class="subtitle">Student Consumer Movement</div>
                </div>
            </div>
            <a href="about.php" class="about-btn">
                <span>ℹ️</span>
                About GPS
            </a>
        </div>
        <div class="header-divider"></div>
    </header>
    
    <div class="container">
        <section class="welcome-section">
            <h1 class="welcome-title">Welcome to GPS UTM Portal</h1>
            <p class="welcome-text">
                Join the Student Consumer Movement and learn to become a smart, ethical, and responsible consumer. 
                Select your portal below to continue.
            </p>
        </section>
        
        <section class="portals-section">
            <!-- New Student Portal -->
            <div class="portal-card blue">
                <div class="portal-icon">👤+</div>
                <h2 class="portal-title">New Student</h2>
                <p class="portal-description">Register for GPS membership</p>
                <ul class="portal-features">
                    <li>Submit membership registration</li>
                    <li>Track registration status</li>
                    <li>View application updates</li>
                </ul>
                <a href="register.php" class="portal-btn">Student Portal</a>
            </div>
            
            <!-- GPS Member Portal -->
            <div class="portal-card green">
                <div class="portal-icon">👤+</div>
                <h2 class="portal-title">GPS Member</h2>
                <p class="portal-description">Access member portal and events</p>
                <ul class="portal-features">
                    <li>View upcoming events</li>
                    <li>Join as crew or helper</li>
                    <li>Track your participations</li>
                </ul>
                <a href="login.php" class="portal-btn">Member Portal</a>
            </div>
            
            <!-- Administrator Portal -->
            <div class="portal-card red">
                <div class="portal-icon">🛡️✓</div>
                <h2 class="portal-title">Administrator</h2>
                <p class="portal-description">Manage club and events</p>
                <ul class="portal-features">
                    <li>Review registrations</li>
                    <li>Create and manage events</li>
                    <li>Manage crew applications</li>
                </ul>
                <a href="login.php" class="portal-btn">Admin Portal</a>
            </div>
        </section>
    </div>
    
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About GPS UTM</h3>
                <p>Gerakan Pengguna Siswa UTM (GPS UTM) is a student consumer movement dedicated to promoting smart, ethical, and responsible consumer practices among students.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="index.php">Home</a></p>
                <p><a href="about.php">About GPS</a></p>
                <p><a href="register.php">Student Registration</a></p>
                <p><a href="login.php">Member Login</a></p>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Universiti Teknologi Malaysia</p>
                <p>81310 UTM Johor Bahru</p>
                <p>Johor, Malaysia</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook">📘</a>
                    <a href="#" aria-label="Instagram">📷</a>
                    <a href="#" aria-label="Twitter">🐦</a>
                    <a href="#" aria-label="Email">✉️</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Gerakan Pengguna Siswa UTM. All rights reserved.</p>
        </div>
    </footer>
    
    <?php include('chatbot.php'); ?>
</body>
</html>
