<?php
session_start();
if($_SESSION['role'] != 'student') header("Location: login.php");
?>
<h2>Welcome, <?php echo $_SESSION['name']; ?>!</h2>
<p>Your account is currently under review by the GPS Admin.</p>
<p>Once approved, you’ll be able to join events.</p>
<a href="logout.php">Logout</a>
