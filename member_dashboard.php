<?php
session_start();
if($_SESSION['role'] != 'member') header("Location: login.php");
?>
<h2>Welcome, <?php echo $_SESSION['name']; ?>!</h2>
<p>You are an approved GPS Member 🎉</p>
<p>Feature available soon: View and Join Events</p>
<a href="logout.php">Logout</a>
