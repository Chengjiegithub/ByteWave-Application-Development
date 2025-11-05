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
        $message = "<div class='msg error'>Please enter your TAC code.</div>";
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
                $message = "<div class='msg error'>Invalid or expired TAC. Please try again.</div>";
            }
        } else {
            $message = "<div class='msg error'>User not found. Please log in again.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>GPSphere | Verify TAC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 40px 60px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            text-align: center;
            width: 340px;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
        }
        input {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            text-align: center;
        }
        button {
            background: #2980b9;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #1c5980;
        }
        .msg {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
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
        a {
            color: #2980b9;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verify TAC</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <input type="text" name="tac" placeholder="Enter 6-digit TAC" maxlength="6" required>
            <button type="submit" name="verify">Verify</button>
        </form>
        <p><a href="login.php">← Back to Login</a></p>
    </div>
</body>
</html>
