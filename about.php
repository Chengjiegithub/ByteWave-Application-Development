<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About GPS | Gerakan Pengguna Siswa UTM</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/about.css">
</head>
<body>
    <div class="back-button-shell">
        <a href="index.php" class="back-button" title="Back to Home">
            <span class="icon">←</span>
            <span>Back</span>
        </a>
    </div>
    
    <div class="main-content">
        <div class="content-card">
            <div class="card-top-border"></div>
            <div class="card-content">
                <h1 class="card-heading">What is GPS?</h1>
                <p class="card-text">
                    Gerakan Pengguna Siswa (GPS), or Student Consumer Movement, is a school-based program that aims to educate students to become smart, ethical, and responsible consumers.
                </p>
                <p class="card-text">
                    Through this movement, students learn about consumer rights and responsibilities, how to make wise spending decisions, and the importance of avoiding waste and fraud.
                </p>
                
                <div class="feature-cards">
                    <div class="feature-card blue">
                        <h3 class="feature-card-title">Consumer Rights</h3>
                        <p class="feature-card-description">Learn about your rights as a consumer and how to protect them</p>
                    </div>
                    
                    <div class="feature-card red">
                        <h3 class="feature-card-title">Wise Spending</h3>
                        <p class="feature-card-description">Make informed decisions about your purchases and finances</p>
                    </div>
                    
                    <div class="feature-card yellow">
                        <h3 class="feature-card-title">Avoid Waste</h3>
                        <p class="feature-card-description">Understanding the importance of sustainability and avoiding fraud</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p class="footer-text">© 2025 Gerakan Pengguna Siswa UTM. All rights reserved.</p>
    </footer>
    
    <?php include('chatbot.php'); ?>
</body>
</html>

