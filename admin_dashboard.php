<?php
session_start();
include('config.php');

// 🧩 Access Control
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$name = $_SESSION['name'] ?? 'Admin';
$msg = "";

// ✅ Approve or reject members
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE users SET role='member', status='approved' WHERE id=$id");
    $msg = "<div class='msg success'>✅ Member approved successfully.</div>";
}
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    $conn->query("DELETE FROM users WHERE id=$id");
    $msg = "<div class='msg error'>🗑 Member removed successfully.</div>";
}

// ✅ Approve or reject join requests
if (isset($_GET['req_approve'])) {
    $req_id = intval($_GET['req_approve']);
    $conn->query("UPDATE event_requests SET status='approved' WHERE id=$req_id");
    $conn->query("UPDATE event_roles SET slots = GREATEST(slots - 1, 0) WHERE id=(SELECT role_id FROM event_requests WHERE id=$req_id)");
    $msg = "<div class='msg success'>✅ Member approved for event role.</div>";
}
if (isset($_GET['req_reject'])) {
    $req_id = intval($_GET['req_reject']);
    $conn->query("UPDATE event_requests SET status='rejected' WHERE id=$req_id");
    $msg = "<div class='msg error'>❌ Join request rejected.</div>";
}

// ✅ Remove crew
if (isset($_GET['remove_crew'])) {
    $req_id = intval($_GET['remove_crew']);
    $conn->query("UPDATE event_roles SET slots = slots + 1 WHERE id=(SELECT role_id FROM event_requests WHERE id=$req_id)");
    $conn->query("DELETE FROM event_requests WHERE id=$req_id");
    $msg = "<div class='msg error'>🗑 Crew member removed successfully.</div>";
}

// ✅ Mark event as finished
if (isset($_GET['finish_event'])) {
    $eid = intval($_GET['finish_event']);
    $conn->query("UPDATE events SET status='finished' WHERE id=$eid");
    $msg = "<div class='msg success'>🏁 Event marked as finished.</div>";
}

// 🧠 Fetch members and events
$pending_members = $conn->query("SELECT id, name, email FROM users WHERE role='student' AND status='pending'");
$approved_members = $conn->query("SELECT id, name, email FROM users WHERE role='member' AND status='approved'");
$events = $conn->query("SELECT * FROM events WHERE status='ongoing' ORDER BY event_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | GPSphere</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f7;
    margin: 0;
    padding: 30px;
}
h1, h2 {
    color: #2c3e50;
}
.card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 25px;
}
h3 {
    background: #2980b9;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}
th {
    background: #2980b9;
    color: white;
}
.btn {
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    font-size: 13px;
}
.approve { background: #27ae60; }
.reject { background: #e74c3c; }
.finish { background: #f39c12; }
.msg {
    margin: 15px 0;
    padding: 10px;
    border-radius: 6px;
    width: 70%;
}
.success { background: #d4edda; color: #155724; }
.error { background: #f8d7da; color: #721c24; }
</style>
</head>
<body>

<h1>Admin Dashboard</h1>
<p>Welcome, <b><?= htmlspecialchars($name) ?></b> 👋 | <a href="logout.php" class="btn reject">Logout</a></p>
<?= $msg ?>

<!-- 🧩 Pending Student Approvals -->
<div class="card">
  <h3>Pending Student Approvals</h3>
  <?php if ($pending_members && $pending_members->num_rows > 0): ?>
  <table>
    <tr><th>Name</th><th>Email</th><th>Action</th></tr>
    <?php while($p = $pending_members->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($p['name'] ?? 'Unknown') ?></td>
      <td><?= htmlspecialchars($p['email'] ?? 'Unknown') ?></td>
      <td>
        <a href="?approve=<?= $p['id'] ?>" class="btn approve">Approve</a>
        <a href="?remove=<?= $p['id'] ?>" class="btn reject">Reject</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
  <p><i>No pending student registrations.</i></p>
  <?php endif; ?>
</div>

<!-- 🧩 Approved Members -->
<div class="card">
  <h3>Approved Members</h3>
  <?php if ($approved_members && $approved_members->num_rows > 0): ?>
  <table>
    <tr><th>Name</th><th>Email</th><th>Action</th></tr>
    <?php while($a = $approved_members->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($a['name'] ?? 'Unknown') ?></td>
      <td><?= htmlspecialchars($a['email'] ?? 'Unknown') ?></td>
      <td><a href="?remove=<?= $a['id'] ?>" class="btn reject">Remove</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
  <p><i>No approved members found.</i></p>
  <?php endif; ?>
</div>

<!-- 🧩 Event Management Section -->
<h2>Ongoing Events</h2>
<?php if ($events && $events->num_rows > 0): ?>
  <?php while($e = $events->fetch_assoc()): ?>
  <div class="card">
    <h3><?= htmlspecialchars($e['event_name']) ?></h3>
    <p><b>Date:</b> <?= $e['event_date'] ?> | <b>Time:</b> <?= $e['event_time'] ?><br>
       <b>Venue:</b> <?= htmlspecialchars($e['location']) ?></p>
    <p><?= nl2br(htmlspecialchars($e['description'])) ?></p>
    <table>
      <tr><th>Role</th><th>Slots</th><th>Approved Members</th><th>Pending Requests</th></tr>
      <?php
      $roles = $conn->query("SELECT * FROM event_roles WHERE event_id={$e['id']}");
      while($r = $roles->fetch_assoc()):
          $role_id = $r['id'];
          $role_name = $r['role_name'];
          $slots = $r['slots'];
          $approved = $conn->query("SELECT COUNT(*) AS c FROM event_requests WHERE role_id=$role_id AND status='approved'")->fetch_assoc()['c'];
          $approved_list = $conn->query("SELECT er.id AS req_id, u.name FROM event_requests er JOIN users u ON er.user_id=u.id WHERE er.role_id=$role_id AND er.status='approved'");
          $members = "";
          while($a = $approved_list->fetch_assoc()){
              $members .= "<div><b>".htmlspecialchars($a['name'] ?? 'Unknown')."</b> <a href='?remove_crew={$a['req_id']}' class='btn reject'>Remove</a></div>";
          }
          if (!$members) $members = "<i>None</i>";
          $pending = $conn->query("SELECT er.id, u.name FROM event_requests er JOIN users u ON er.user_id=u.id WHERE er.role_id=$role_id AND er.status='pending'");
      ?>
      <tr>
        <td><?= htmlspecialchars($role_name) ?></td>
        <td><?= $approved ?> / <?= $slots ?></td>
        <td><?= $members ?></td>
        <td>
          <?php if ($pending->num_rows > 0): ?>
            <?php while($p = $pending->fetch_assoc()): ?>
              <?= htmlspecialchars($p['name'] ?? 'Unknown') ?>
              <a href="?req_approve=<?= $p['id'] ?>" class="btn approve">Approve</a>
              <a href="?req_reject=<?= $p['id'] ?>" class="btn reject">Reject</a><br>
            <?php endwhile; ?>
          <?php else: ?>
            <i>No pending requests.</i>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
    <br><a href="?finish_event=<?= $e['id'] ?>" class="btn finish">Finish Event</a>
  </div>
  <?php endwhile; ?>
<?php else: ?>
  <p><i>No ongoing events available.</i></p>
<?php endif; ?>

</body>
</html>
