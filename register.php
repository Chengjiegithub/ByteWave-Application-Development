<?php
include('config.php');
session_start();

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // --- Basic validation ---
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $error = "Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.";
    } else {
        // --- Check if email already exists ---
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already registered. Please login.";
        } else {
            // --- Insert new user ---
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = "student";
            $status = "pending";

            $insert = $conn->prepare(
                "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)"
            );
            $insert->bind_param("sssss", $name, $email, $hash, $role, $status);

            if ($insert->execute()) {
                $success = "Registration successful! Your account is pending approval by the admin.";
            } else {
                $error = "Registration failed. Please try again.";
            }
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
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/register.css">
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
            <div class="back-button-shell">
            <a href="index.php" class="back-button">
                <span class="icon">←</span>
                <span>Back</span>
            </a>
        </div>
        </div>
        
        <div class="header-divider"></div>
    </header>
    
    <div class="main-content">
        <div class="form-card">
            <div class="form-icon">👤</div>
            <p class="form-title">Login or register for GPS membership</p>
            
            <div class="tabs">
                <button class="tab" onclick="window.location.href='login.php'">Login</button>
                <button class="tab active" onclick="window.location.href='register.php'">Register</button>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="msg error"><?= htmlspecialchars($error) ?></div>
            <?php elseif (isset($success)): ?>
                <div class="msg success"><?= htmlspecialchars($success) ?><br><a href="login.php" style="color: #155724; text-decoration: underline;">Proceed to Login</a></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input type="text" name="name" class="form-input" placeholder="Enter your full name" required>
                    </div>
                </div>
                
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
                
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="confirm" id="confirm-password" class="form-input password-input" placeholder="Confirm your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm-password')">👁️</button>
                    </div>
                </div>
                
                <button type="submit" name="register" class="submit-btn">Register</button>
            </form>
            
            <p style="text-align: center; margin-top: 15px; font-size: 14px; color: #666;">
                Already have an account? <a href="login.php" style="color: #2980b9; text-decoration: none;">Login here</a>
            </p>
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
