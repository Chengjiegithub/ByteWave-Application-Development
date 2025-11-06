<?php
session_start();
include('config.php');

if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$name  = $_SESSION['name'] ?? 'Admin';
$msg   = "";

// ✅ Approve / Reject join requests
if (isset($_GET['req_approve'])) {
    $req_id = intval($_GET['req_approve']);
    $conn->query("UPDATE event_requests SET status='approved' WHERE id=$req_id");
    $conn->query("UPDATE event_roles SET slots = GREATEST(slots - 1, 0) WHERE id=(SELECT role_id FROM event_requests WHERE id=$req_id)");
    $msg = "<div class='msg success'>✅ Member approved.</div>";
}
if (isset($_GET['req_reject'])) {
    $req_id = intval($_GET['req_reject']);
    $conn->query("UPDATE event_requests SET status='rejected' WHERE id=$req_id");
    $msg = "<div class='msg error'>❌ Request rejected.</div>";
}

// ✅ Remove crew
if (isset($_GET['remove_crew'])) {
    $req_id = intval($_GET['remove_crew']);
    $conn->query("UPDATE event_roles SET slots = slots + 1 WHERE id=(SELECT role_id FROM event_requests WHERE id=$req_id)");
    $conn->query("DELETE FROM event_requests WHERE id=$req_id");
    $msg = "<div class='msg error'>🗑 Crew member removed.</div>";
}

// ✅ Change crew role (simple PHP method)
if (isset($_POST['change_role'])) {
    $req_id = intval($_POST['req_id']);
    $new_role_id = intval($_POST['new_role_id']);

    $info = $conn->query("
        SELECT er.user_id, er.role_id AS old_role_id, e.event_name,
               r1.role_name AS old_role, r2.role_name AS new_role
        FROM event_requests er
        JOIN event_roles r1 ON er.role_id = r1.id
        JOIN event_roles r2 ON r2.id = $new_role_id
        JOIN events e ON r1.event_id = e.id
        WHERE er.id = $req_id
    ")->fetch_assoc();

    if ($info) {
        $user_id = $info['user_id'];
        $old_role_id = $info['old_role_id'];
        $old_role = $info['old_role'];
        $new_role = $info['new_role'];
        $event_name = $info['event_name'];

        $conn->query("UPDATE event_roles SET slots = slots + 1 WHERE id=$old_role_id");
        $conn->query("UPDATE event_requests SET role_id=$new_role_id WHERE id=$req_id");
        $conn->query("UPDATE event_roles SET slots = GREATEST(slots - 1, 0) WHERE id=$new_role_id");

        // Create notification
        $message = "Your role in '$event_name' has been changed from $old_role to $new_role by the admin.";
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();
        $stmt->close();

        $msg = "<div class='msg success'>✅ Role changed successfully and member notified.</div>";
    } else {
        $msg = "<div class='msg error'>❌ Failed to change role.</div>";
    }
}

// ✅ Mark event as finished
if (isset($_GET['finish_event'])) {
    $eid = intval($_GET['finish_event']);
    $conn->query("UPDATE events SET status='finished' WHERE id=$eid");
    $msg = "<div class='msg success'>🏁 Event marked as finished.</div>";
}

// --- Query ongoing events ---
$events = $conn->query("SELECT * FROM events WHERE status='ongoing'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GPSphere | Admin Dashboard</title>
<style>
body{font-family:Arial;background:#f4f6f7;margin:0;padding:30px;}
h1{color:#2c3e50;}
.card{background:#fff;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1);padding:20px;margin-bottom:25px;}
h3{background:#2980b9;color:white;padding:8px 15px;border-radius:5px;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{padding:10px;border-bottom:1px solid #ddd;text-align:left;}
th{background:#2980b9;color:white;}
.btn{padding:5px 10px;border-radius:5px;text-decoration:none;color:white;font-size:13px;}
.approve{background:#27ae60;} .reject{background:#e74c3c;} .finish{background:#f39c12;}
.msg{margin:15px 0;padding:10px;border-radius:6px;width:70%;}
.success{background:#d4edda;color:#155724;} .error{background:#f8d7da;color:#721c24;}
select{padding:4px;}
</style>
</head>
<body>
<h1>Admin Dashboard</h1>
<p>Welcome, <b><?= htmlspecialchars($name) ?></b> 👋 | <a href="logout.php" class="btn reject">Logout</a></p>
<?= $msg ?>

<?php if ($events->num_rows>0): while($e=$events->fetch_assoc()): ?>
<div class="card">
  <h3><?= htmlspecialchars($e['event_name']) ?></h3>
  <p><b>Date:</b> <?= $e['event_date'] ?> | <b>Time:</b> <?= $e['event_time'] ?><br><b>Venue:</b> <?= htmlspecialchars($e['location']) ?></p>
  <p><?= nl2br(htmlspecialchars($e['description'])) ?></p>
  <table>
    <tr><th>Role</th><th>Slots</th><th>Approved Members</th><th>Pending Requests</th></tr>
    <?php
    $roles=$conn->query("SELECT * FROM event_roles WHERE event_id={$e['id']}");
    while($r=$roles->fetch_assoc()):
      $role_id=$r['id'];
      $role_name=$r['role_name'];
      $slots=$r['slots'];
      $approved_q=$conn->query("SELECT COUNT(*) AS c FROM event_requests WHERE role_id=$role_id AND status='approved'");
      $approved_count=$approved_q->fetch_assoc()['c'];
      $approved_list=$conn->query("SELECT er.id AS req_id,u.name FROM event_requests er JOIN users u ON er.user_id=u.id WHERE er.role_id=$role_id AND er.status='approved'");
      $members="";
      while($a=$approved_list->fetch_assoc()){
          $req=$a['req_id']; $n=$a['name'];
          $members.="
          <div>
            <b>$n</b>
            <form method='POST' style='display:inline;'>
              <input type='hidden' name='req_id' value='$req'>
              <select name='new_role_id'>
              ";
              $opts=$conn->query("SELECT id,role_name,slots FROM event_roles WHERE event_id={$e['id']} AND slots>0");
              while($o=$opts->fetch_assoc()){
                $members.="<option value='{$o['id']}'>{$o['role_name']} ({$o['slots']} slots)</option>";
              }
              $members.="</select>
              <button name='change_role' class='btn approve'>Change</button>
            </form>
            <a href='?remove_crew=$req' class='btn reject'>Remove</a>
          </div>";
      }
      if(!$members) $members="<i>None</i>";
      $pending=$conn->query("SELECT er.id,u.name FROM event_requests er JOIN users u ON er.user_id=u.id WHERE er.role_id=$role_id AND er.status='pending'");
    ?>
    <tr>
      <td><?= $role_name ?></td>
      <td><?= $approved_count ?> / <?= $slots ?></td>
      <td><?= $members ?></td>
      <td>
        <?php if ($pending->num_rows>0): while($p=$pending->fetch_assoc()): ?>
          <?= $p['name'] ?> <a href="?req_approve=<?= $p['id'] ?>" class="btn approve">Approve</a>
          <a href="?req_reject=<?= $p['id'] ?>" class="btn reject">Reject</a><br>
        <?php endwhile; else: ?><i>No pending requests.</i><?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
  <br><a href="?finish_event=<?= $e['id'] ?>" class="btn finish">Finish Event</a>
</div>
<?php endwhile; endif; ?>
</body>
</html>
