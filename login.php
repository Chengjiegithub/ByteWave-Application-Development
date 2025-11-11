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
            display: flex;
            flex-direction: column;
            color: #2c3e50;
        }
        
        .header {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-top {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .back-link {
            color: #2980b9;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #1c5980;
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
            font-size: 16px;
            color: #666;
        }
        
        .header-divider {
            height: 2px;
            background: #2980b9;
            width: 100%;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #e3f2fd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px;
            color: #2980b9;
        }
        
        .form-title {
            text-align: center;
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 25px;
        }
        
        .tabs {
            display: flex;
            gap: 0;
            margin-bottom: 25px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #999;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .tab.active {
            color: #2980b9;
            background: white;
            border-bottom-color: #2980b9;
            font-weight: 600;
        }
        
        .tab:not(.active):hover {
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #999;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 40px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-input.password-input {
            padding-right: 45px;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #2980b9;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #999;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s;
        }
        
        .password-toggle:hover {
            color: #2980b9;
        }
        
        .forgot-link {
            display: block;
            text-align: right;
            margin-top: -10px;
            margin-bottom: 20px;
            color: #2980b9;
            text-decoration: none;
            font-size: 14px;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: #2980b9;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .submit-btn:hover {
            background: #1c5980;
        }
        
        .msg {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
        }
        
        .privacy-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-left: 4px solid #27ae60;
        }
        
        .privacy-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
            color: #27ae60;
        }
        
        .privacy-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .privacy-list {
            list-style: none;
        }
        
        .privacy-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            color: #555;
            font-size: 14px;
        }
        
        .privacy-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #27ae60;
            font-weight: bold;
            font-size: 16px;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 25px 20px;
        }
        
        .footer-text {
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }
            
            .form-card, .privacy-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-top">
            <a href="index.php" class="back-link">← Back</a>
            <div class="logo-section">
                <div class="logo-circle">GERAKAN PENGGUNA SISWA UTM</div>
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
