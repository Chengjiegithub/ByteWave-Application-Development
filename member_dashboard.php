<?php
include('config.php');
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] != 'member') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$name  = $_SESSION['name'] ?? 'Member';

// Fetch logged-in user info
$user_q = $conn->prepare("SELECT id FROM users WHERE email=?");
$user_q->bind_param("s", $email);
$user_q->execute();
$user = $user_q->get_result()->fetch_assoc();
$user_id = $user['id'];

// Handle join request
if (isset($_POST['join'])) {
    $event_id = intval($_POST['event_id']);
    $role_id  = intval($_POST['role_id']);

    // Check if already requested
    $check = $conn->prepare("SELECT id FROM event_requests WHERE event_id=? AND user_id=?");
    $check->bind_param("ii", $event_id, $user_id);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        $msg = "<div class='msg error'>⚠️ You have already requested to join this event.</div>";
    } else {
        $ins = $conn->prepare("INSERT INTO event_requests (event_id, user_id, role_id) VALUES (?,?,?)");
        $ins->bind_param("iii", $event_id, $user_id, $role_id);
        if ($ins->execute()) {
            $msg = "<div class='msg success'>✅ Your join request has been sent for approval.</div>";
        } else {
            $msg = "<div class='msg error'>❌ Failed to send request. Try again.</div>";
        }
    }
}

// Fetch all events
$events = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GPSphere | Member Dashboard</title>
<style>
body {font-family:Arial;background:#f4f6f7;padding:40px;}
h1{color:#2c3e50;}
.card{background:white;padding:20px;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,0.1);width:90%;margin:20px auto;}
.event-header{background:#2980b9;color:white;padding:8px 10px;border-radius:6px;}
.msg{margin:15px 0;padding:10px;border-radius:6px;width:60%;}
.success{background:#d4edda;color:#155724;}
.error{background:#f8d7da;color:#721c24;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{padding:10px;border-bottom:1px solid #eee;}
th{background:#2980b9;color:white;}
form.inline{display:inline;}
select{padding:5px;}
.joinbtn{background:#27ae60;color:white;border:none;padding:6px 12px;border-radius:4px;cursor:pointer;}
.joinbtn:hover{background:#219150;}
.logout{background:#34495e;color:white;padding:8px 15px;border-radius:5px;text-decoration:none;}
.logout:hover{background:#2c3e50;}
</style>
</head>
<body>

<h1>Welcome, <?php echo $name; ?> 👋</h1>
<p>You are logged in as a <b>GPSphere Member</b> (<?php echo $email; ?>).</p>
<a href="logout.php" class="logout">Logout</a>

<?php if(isset($msg)) echo $msg; ?>

<?php if ($events->num_rows > 0): ?>
    <?php while($ev = $events->fetch_assoc()): ?>
        <div class="card">
            <div class="event-header"><b><?php echo $ev['event_name']; ?></b></div>
            <p><b>Date:</b> <?php echo $ev['event_date']; ?> &nbsp;&nbsp;
               <b>Time:</b> <?php echo $ev['event_time']; ?> <br>
               <b>Venue:</b> <?php echo $ev['location']; ?></p>
            <p><?php echo nl2br($ev['description']); ?></p>

            <table>
                <tr><th>Position</th><th>Slots</th><th>Action</th></tr>
                <?php
                $roles = $conn->query("SELECT * FROM event_roles WHERE event_id=".$ev['id']);
                if ($roles->num_rows > 0):
                    while($r = $roles->fetch_assoc()):
                        // check if this role already full
                        $count = $conn->query("SELECT COUNT(*) AS c FROM event_requests WHERE role_id={$r['id']} AND status='approved'")->fetch_assoc();
                        $remaining = $r['slots'] - $count['c'];
                ?>
                <tr>
                    <td><?php echo $r['role_name']; ?></td>
                    <td><?php echo $remaining . " / " . $r['slots']; ?></td>
                    <td>
                        <?php if ($remaining > 0): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="event_id" value="<?php echo $ev['id']; ?>">
                            <input type="hidden" name="role_id" value="<?php echo $r['id']; ?>">
                            <button type="submit" name="join" class="joinbtn">Join</button>
                        </form>
                        <?php else: ?>
                            <span style="color:#888;">Full</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="3">No crew roles defined for this event.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No events available yet.</p>
<?php endif; ?>

</body>
</html>
