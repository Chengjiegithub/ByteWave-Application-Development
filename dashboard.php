<?php
session_start();
if(!isset($_SESSION['role'])) header("Location: login.php");
?>
<h2>Welcome to GPSphere Dashboard</h2>
<p>Logged in as: <?php echo $_SESSION['role']; ?></p>
<a href="logout.php">Logout</a>