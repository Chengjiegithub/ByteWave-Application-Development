<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerakan Pengguna Siswa UTM | Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            color: #2c3e50;
        }
        
        .header {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2980b9 0%, #1c5980 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            padding: 5px;
            line-height: 1.2;
        }
        
        .header-text {
            display: flex;
            flex-direction: column;
        }
        
        .main-title {
            font-size: 24px;
            font-weight: bold;
            color: #1a5490;
            margin-bottom: 2px;
        }
        
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        
        .about-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: 2px solid #2980b9;
            background: white;
            color: #2980b9;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .about-btn:hover {
            background: #2980b9;
            color: white;
        }
        
        .header-divider {
            height: 2px;
            background: #2980b9;
            width: 100%;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }
        
        .welcome-section {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .welcome-title {
            font-size: 42px;
            color: #1a5490;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .welcome-text {
            font-size: 18px;
            color: #555;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .portals-section {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .portal-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            width: 320px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-top: 4px solid;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .portal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .portal-card.blue {
            border-top-color: #2980b9;
        }
        
        .portal-card.green {
            border-top-color: #27ae60;
        }
        
        .portal-card.red {
            border-top-color: #e74c3c;
        }
        
        .portal-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px;
        }
        
        .portal-card.blue .portal-icon {
            background: #e3f2fd;
            color: #2980b9;
        }
        
        .portal-card.green .portal-icon {
            background: #e8f5e9;
            color: #27ae60;
        }
        
        .portal-card.red .portal-icon {
            background: #ffebee;
            color: #e74c3c;
        }
        
        .portal-title {
            font-size: 22px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .portal-description {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .portal-features {
            list-style: none;
            margin-bottom: 25px;
        }
        
        .portal-features li {
            padding: 8px 0;
            color: #555;
            font-size: 14px;
            padding-left: 20px;
            position: relative;
        }
        
        .portal-features li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #999;
            font-size: 18px;
        }
        
        .portal-btn {
            display: block;
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .portal-card.blue .portal-btn {
            background: #2980b9;
        }
        
        .portal-card.blue .portal-btn:hover {
            background: #1c5980;
        }
        
        .portal-card.green .portal-btn {
            background: #27ae60;
        }
        
        .portal-card.green .portal-btn:hover {
            background: #229954;
        }
        
        .portal-card.red .portal-btn {
            background: #e74c3c;
        }
        
        .portal-card.red .portal-btn:hover {
            background: #c0392b;
        }
        
        /* Chatbot positioning - bottom right */
        #chatbot-container {
            position: fixed !important;
            bottom: 20px !important;
            right: 25px !important;
            left: auto !important;
        }
        
        #chatbot-box {
            left: auto !important;
            right: 0 !important;
        }
        
        @media (max-width: 768px) {
            .header-top {
                flex-direction: column;
                gap: 15px;
            }
            
            .logo-section {
                flex-direction: column;
                text-align: center;
            }
            
            .welcome-title {
                font-size: 28px;
            }
            
            .portals-section {
                flex-direction: column;
                align-items: center;
            }
            
            .portal-card {
                width: 100%;
                max-width: 400px;
            }
            
            #chatbot-container {
                bottom: 15px;
                right: 15px;
                left: auto;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-top">
            <div class="logo-section">
                <div class="logo-circle">GERAKAN PENGGUNA SISWA UTM</div>
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
    
    <?php include('chatbot.php'); ?>
</body>
</html>
