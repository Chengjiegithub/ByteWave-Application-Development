<?php
include('config.php');
session_start();

// --- Access Control: Students Only ---
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$name = $_SESSION['name'] ?? 'Student';

// Get student record to check status
$stmt = $conn->prepare("SELECT status FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$status = strtolower($user['status'] ?? 'pending');
?>

<!DOCTYPE html>
<html>
<head>
    <title>GPSphere | Student Dashboard</title>
    <link rel="stylesheet" href="assests/css/base.css">
    <link rel="stylesheet" href="assests/css/student_dashboard.css">
</head>
<body>
    <div class="back-button-shell">
        <a href="login.php" class="back-button" title="Back to Login">
            <span class="icon">←</span>
            <span>Back</span>
        </a>
    </div>
    <div class="container">
        <h2>Welcome, <?php echo $name; ?> 👋</h2>
        <p>Your registered email: <b><?php echo $email; ?></b></p>

        <?php if ($status == 'approved'): ?>
            <div class="status approved">
                🎉 Congratulations! Your membership has been approved. 
                <br>You can now access the <a href="member_dashboard.php">Member Dashboard</a>.
            </div>
        <?php else: ?>
            <div class="status">
                ⏳ Your registration is pending approval by the GPS Admin. 
                <br>Please check again later.
            </div>
        <?php endif; ?>

        <a href="logout.php" class="logout">Logout</a>
    </div>

    <!-- Chatbot here -->
    <?php include('chatbot.php'); ?>

</body>
</html>
