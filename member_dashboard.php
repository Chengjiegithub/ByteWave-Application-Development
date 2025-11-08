<?php
session_start();
include('config.php');

if (!isset($_SESSION['email']) || $_SESSION['role'] != 'member') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$name = $_SESSION['name'] ?? 'Member';
$msg = "";

// ✅ Show notifications
$user = $conn->query("SELECT id FROM users WHERE email='$email'")->fetch_assoc();
$user_id = $user['id'];
$notifications = $conn->query("SELECT * FROM notifications WHERE user_id=$user_id AND is_read=0 ORDER BY created_at DESC");

// ✅ Handle Join Requests
if (isset($_GET['join_role'])) {
    $role_id = intval($_GET['join_role']);
    $event_id = $conn->query("SELECT event_id FROM event_roles WHERE id=$role_id")->fetch_assoc()['event_id'];
    $check = $conn->query("SELECT * FROM event_requests WHERE user_id=$user_id AND event_id=$event_id");
    if ($check->num_rows > 0) {
        $msg = "<div class='msg error'>⚠️ You already joined or have a pending request for this event.</div>";
    } else {
        $conn->query("INSERT INTO event_requests (user_id,event_id,role_id,status) VALUES ($user_id,$event_id,$role_id,'pending')");
        $msg = "<div class='msg success'>✅ Request sent successfully!</div>";
    }
}

$events = $conn->query("SELECT * FROM events WHERE status='ongoing'");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Member Dashboard</title>
<style>
body{font-family:Arial;background:#f4f6f7;margin:0;padding:30px;}
h1{color:#2c3e50;}
.card{background:#fff;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1);padding:20px;margin-bottom:25px;}
h2{background:#2980b9;color:white;padding:10px;border-radius:5px;}
.msg{margin:15px 0;padding:10px;border-radius:6px;width:70%;}
.success{background:#d4edda;color:#155724;} .error{background:#f8d7da;color:#721c24;}
.you{color:#27ae60;font-weight:bold;}
.status-approved{color:green;font-weight:bold;} .status-pending{color:orange;font-weight:bold;} .status-rejected{color:red;font-weight:bold;} .status-full{color:red;font-weight:bold;}
</style>
</head>
<body>
<h1>Welcome, <?= htmlspecialchars($name) ?> 👋</h1>
<a href="logout.php" style="background:#34495e;color:#fff;padding:6px 12px;border-radius:5px;text-decoration:none;">Logout</a>

<?php
if ($notifications->num_rows > 0) {
    echo "<div style='background:#fff3cd;color:#856404;padding:10px;border-radius:6px;margin-top:10px;'>
          <b>🔔 Notifications:</b><ul>";
    while ($n = $notifications->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($n['message']) . " <small>(" . htmlspecialchars($n['created_at']) . ")</small></li>";
    }
    echo "</ul></div>";
    $conn->query("UPDATE notifications SET is_read=1 WHERE user_id=$user_id");
}
?>

<?= $msg ?>
<h2>Your Member Dashboard</h2>

<?php if ($events->num_rows>0): while($e=$events->fetch_assoc()): ?>
<div class="card">
  <h2><?= htmlspecialchars($e['event_name']) ?></h2>
  <p><b>Date:</b> <?= $e['event_date'] ?> | <b>Time:</b> <?= $e['event_time'] ?><br><b>Venue:</b> <?= htmlspecialchars($e['location']) ?></p>
  <p><?= nl2br(htmlspecialchars($e['description'])) ?></p>
  <table width="100%" border="1" cellspacing="0" cellpadding="6">
    <tr><th>Position</th><th>Slots</th><th>Approved Members</th><th>Action</th></tr>
    <?php
    $roles=$conn->query("SELECT * FROM event_roles WHERE event_id={$e['id']}");
    while($r=$roles->fetch_assoc()):
        $role_id=$r['id']; $role_name=$r['role_name']; $slots=$r['slots'];
        $approved=$conn->query("SELECT COUNT(*) AS c FROM event_requests WHERE role_id=$role_id AND status='approved'")->fetch_assoc()['c'];
        $members_q=$conn->query("SELECT u.name FROM event_requests er JOIN users u ON er.user_id=u.id WHERE er.role_id=$role_id AND er.status='approved'");
        $names=[]; while($m=$members_q->fetch_assoc()){ $names[]=$m['name']==$name?"<b style='color:green;'>".$m['name']." (You)</b>":$m['name']; }
        $display=$names?implode(', ',$names):'<i>None</i>';
        $st=$conn->query("SELECT er.status FROM event_requests er WHERE er.role_id=$role_id AND er.user_id=$user_id");
        $status=$st->num_rows>0?$st->fetch_assoc()['status']:'';
    ?>
    <tr>
      <td><?= $role_name ?></td>
      <td><?= $approved ?> / <?= $slots ?></td>
      <td><?= $display ?></td>
      <td>
        <?php
        if($status=='approved') echo "<span class='status-approved'>Approved</span>";
        elseif($status=='pending') echo "<span class='status-pending'>Pending</span>";
        elseif($status=='rejected') echo "<span class='status-rejected'>Rejected</span>";
        elseif($approved >= $slots) echo "<span class='status-full'>Full</span>";
        else echo "<a href='?join_role=$role_id' style='background:#27ae60;color:#fff;padding:6px 10px;border-radius:5px;text-decoration:none;'>Join</a>";
        ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php endwhile; endif; ?>

<?php include('chatbot.php'); ?>
</body>
</html>
