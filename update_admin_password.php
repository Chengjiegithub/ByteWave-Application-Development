<?php
include('config.php');

// The password you want for admin
$newPasswordPlain = 'Admin123!';

// Hash it properly
$newHash = password_hash($newPasswordPlain, PASSWORD_DEFAULT);

// Update admin record
$sql = "UPDATE users SET password='$newHash' WHERE email='admin@gpsphere.com'";
if ($conn->query($sql)) {
    echo "✅ Admin password reset successfully.<br>";
    echo "Email: admin@gpsphere.com<br>Password: $newPasswordPlain";
} else {
    echo "❌ Update failed: " . $conn->error;
}
$conn->close();
?>
