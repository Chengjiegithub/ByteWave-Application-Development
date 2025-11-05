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
                    $mail->Username = 'chengjiesu310@gmail.com';   // 🔸 change this to the email you use
                    $mail->Password = 'wyzcvlgiaeztnjem';      // 🔸 Gmail App Password
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

                    $mail->send();
                    $success = "A verification code (TAC) has been sent to your email.";
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
<html>
<head>
    <title>GPSphere | Login</title>
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
        <h2>Login to GPSphere</h2>

        <?php if (isset($error)): ?>
            <div class="msg error"><?= $error ?></div>
        <?php elseif (isset($success)): ?>
            <div class="msg success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <?php if (!empty($redirect)): ?>
            <p><a href="verify.php">Proceed to verification</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
