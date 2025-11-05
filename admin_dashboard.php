<?php
include('config.php');
session_start();
if($_SESSION['role'] != 'admin') header("Location: login.php");

// Handle approval/rejection
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $conn->query("UPDATE users SET status='approved', role='member' WHERE id=$id");
}
if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    $conn->query("DELETE FROM users WHERE id=$id");
}
if(isset($_GET['remove'])){
    $id = $_GET['remove'];
    $conn->query("DELETE FROM users WHERE id=$id");
}

$pending = $conn->query("SELECT * FROM users WHERE role='student' AND status='pending'");
$members = $conn->query("SELECT * FROM users WHERE role='member'");
?>

<h2>Admin Dashboard - Manage Members</h2>

<h3>Pending Applications</h3>
<table border="1" cellpadding="5">
<tr><th>Name</th><th>Email</th><th>Action</th></tr>
<?php while($row = $pending->fetch_assoc()): ?>
<tr>
    <td><?= $row['name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td>
        <a href="?approve=<?= $row['id'] ?>">✅ Approve</a> |
        <a href="?reject=<?= $row['id'] ?>">❌ Reject</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<h3>Approved Members</h3>
<table border="1" cellpadding="5">
<tr><th>Name</th><th>Email</th><th>Action</th></tr>
<?php while($row = $members->fetch_assoc()): ?>
<tr>
    <td><?= $row['name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><a href="?remove=<?= $row['id'] ?>">🗑 Remove</a></td>
</tr>
<?php endwhile; ?>
</table>

<a href="logout.php">Logout</a>
