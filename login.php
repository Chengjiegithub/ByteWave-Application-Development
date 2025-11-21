<?php
include('config.php');
session_start();

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

if (isset($_POST['login'])) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $storedHash = $row['password'] ?? $row['PASSWORD'] ?? '';

            if (password_verify($password, $storedHash)) {
                // Generate TAC
                $tac = rand(100000, 999999);
                $expiry = date("Y-m-d H:i:s", strtotime("+2 minutes"));
                $update = $conn->prepare("UPDATE users SET tac_code=?, tac_expiry=? WHERE email=?");
                $update->bind_param("sss", $tac, $expiry, $email);
                $update->execute();

                $_SESSION['email'] = $email;

                // Send TAC via PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'chengjiesu310@gmail.com';
                    $mail->Password = 'wyzcvlgiaeztnjem';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('chengjiesu310@gmail.com', 'GPSphere');
                    $name = $row['name'] ?? $row['NAME'] ?? 'User';
                    $mail->isHTML(true);
                    $mail->Subject = 'Your GPSphere TAC Code';
                    $mail->Body = "
                        <h3>Hello, $name!</h3>
                        <p>Your TAC Code is: <b>$tac</b></p>
                        <p>This code will expire in 2 minutes.</p>
                        <hr>
                        <p>From: GPSphere UTM Team</p>
                    ";

                    // ---- TEST MODE ----
                    $success = "✅ Test Mode: Your TAC is <b>$tac</b> (expires $expiry)";
                    $redirect = true;

                } catch (Exception $e) {
                    $error = "Email could not be sent. Please try again later.";
                }

            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Email not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Gerakan Pengguna Siswa UTM</title>
    <link rel="stylesheet" href="assests/css/base.css">
    <link rel="stylesheet" href="assests/css/login.css">
</head>
<body>
    <header class="header">
        <div class="header-top">
            <div class="logo-section">
                <img src="assests/Official GPS Logo Coloured.png" alt="GPS Logo" class="logo-img">
                <div class="header-text">
                    <div class="main-title">Gerakan Pengguna Siswa UTM</div>
                    <div class="subtitle">Student Portal</div>
                </div>
            </div>
        </div>
        <div class="header-divider"></div>
    </header>
    
    <div class="main-content">
        <div class="form-card">
            <div class="form-icon">👤+</div>
            <p class="form-title">Login or register for GPS membership</p>
            
            <div class="tabs">
                <button class="tab active" onclick="window.location.href='login.php'">Login</button>
                <button class="tab" onclick="window.location.href='register.php'">Register</button>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="msg error"><?= htmlspecialchars($error) ?></div>
            <?php elseif (isset($success)): ?>
                <div class="msg success"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">✉</span>
                        <input type="email" name="email" class="form-input" placeholder="your.email@utm.my" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" id="password" class="form-input password-input" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">👁️</button>
                    </div>
                </div>
                
                <a href="#" class="forgot-link">Forgot Password?</a>
                
                <button type="submit" name="login" class="submit-btn">Login</button>
            </form>
            
            <?php if (!empty($redirect)): ?>
                <p style="text-align: center; margin-top: 15px;"><a href="verify.php" style="color: #2980b9; text-decoration: none;">Proceed to verification</a></p>
            <?php endif; ?>
        </div>
        
        <div class="privacy-card">
            <div class="privacy-icon">🔒</div>
            <h3 class="privacy-title">Data Protection & Privacy (PDPA)</h3>
            <ul class="privacy-list">
                <li>Data encryption and secure storage</li>
                <li>Minimal data collection</li>
                <li>No data sharing with third parties</li>
                <li>Right to access and delete your data</li>
            </ul>
        </div>
    </div>
    
    <footer class="footer">
        <p class="footer-text">© 2025 Gerakan Pengguna Siswa UTM. All rights reserved.</p>
    </footer>
    
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggle = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = '🙈';
            } else {
                input.type = 'password';
                toggle.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
