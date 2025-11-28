<?php
include('config.php');
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$message = '';

if (isset($_POST['verify'])) {
    $tac = trim($_POST['tac']);

    if (empty($tac)) {
        $error = "Please enter your TAC code.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            $storedTac = $user['tac_code'] ?? $user['TAC_CODE'] ?? null;
            $tacExpiry = $user['tac_expiry'] ?? $user['TAC_EXPIRY'] ?? null;
            $role = strtolower($user['role'] ?? $user['ROLE'] ?? 'student');
            $status = strtolower($user['status'] ?? $user['STATUS'] ?? 'pending');

            // Check TAC and expiry
            if ($storedTac && $storedTac == $tac && strtotime($tacExpiry) > time()) {

                // Clear TAC for security
                $clear = $conn->prepare("UPDATE users SET tac_code=NULL, tac_expiry=NULL WHERE email=?");
                $clear->bind_param("s", $email);
                $clear->execute();

                $_SESSION['role'] = $role;
                $_SESSION['name'] = $user['name'] ?? $user['NAME'] ?? 'User';

                // Redirect based on role
                if ($role == 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($role == 'member' && $status == 'approved') {
                    header("Location: member_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit();

            } else {
                $error = "Invalid or expired TAC. Please try again.";
            }
        } else {
            $error = "User not found. Please log in again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify TAC | Gerakan Pengguna Siswa UTM</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/verify.css">
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
        </div>
        <div class="header-divider"></div>
    </header>
    
    <div class="main-content">
        <div class="form-card">
            <div class="form-icon">🔐</div>
            <h2 class="form-title">Verify TAC Code</h2>
            <p class="form-subtitle">Enter the 6-digit code sent to your email</p>
            
            <?php if (isset($error)): ?>
                <div class="msg error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">TAC Code</label>
                    <input type="text" name="tac" id="tac" class="tac-input" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autocomplete="off">
                </div>
                
                <button type="submit" name="verify" class="submit-btn">Verify</button>
            </form>
            
            <div class="back-to-login">
                <a href="login.php">← Back to Login</a>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p class="footer-text">© 2025 Gerakan Pengguna Siswa UTM. All rights reserved.</p>
    </footer>
    
    <script>
        // Auto-focus and format TAC input
        document.getElementById('tac').focus();
        
        // Only allow numbers
        document.getElementById('tac').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Auto-submit when 6 digits are entered
        document.getElementById('tac').addEventListener('input', function(e) {
            if (this.value.length === 6) {
                // Optional: auto-submit after a short delay
                // setTimeout(() => {
                //     this.form.submit();
                // }, 500);
            }
        });
    </script>
</body>
</html>
