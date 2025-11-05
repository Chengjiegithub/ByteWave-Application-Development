<?php
// test_password_verify.php
include('config.php');

$email = 'admin@gpsphere.com'; // admin email from create_database.php
$passwordToTest = 'Admin123!'; // change this to test other strings

// Fetch admin row
$res = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1");
if (!$res) {
    die("DB error: " . $conn->error);
}
if ($res->num_rows === 0) {
    die("No user found with email $email");
}

$row = $res->fetch_assoc();
$storedHash = $row['password'] ?? $row['PASSWORD'] ?? '';

echo "<h3>Testing password verification for: $email</h3>";
echo "<p><b>Stored hash:</b> " . htmlentities($storedHash) . "</p>";
echo "<p><b>Password being tested:</b> " . htmlentities($passwordToTest) . "</p>";

$ok = password_verify($passwordToTest, $storedHash);
echo "<p><b>password_verify result:</b> " . ($ok ? "<span style='color:green'>TRUE</span>" : "<span style='color:red'>FALSE</span>") . "</p>";

// Also show raw POST debug example (not used here), just for reference:
echo "<hr><p>If you want to test a different password, edit <code>\$passwordToTest</code> above and reload.</p>";
?>
