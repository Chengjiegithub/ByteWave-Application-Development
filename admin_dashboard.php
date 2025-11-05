<?php
include('config.php');
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$name  = $_SESSION['name'] ?? 'Admin';
$msg   = "";

// ---------------- MEMBER ACTIONS ----------------
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE users SET role='member', status='approved' WHERE id=$id");
    $msg = "<div class='msg success'>✅ Member approved successfully.</div>";
}
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("DELETE FROM users WHERE id=$id");
    $msg = "<div class='msg error'>❌ Registration rejected and deleted.</div>";
}
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    $conn->query("DELETE FROM users WHERE id=$id");
    $msg = "<div class='msg error'>🗑 Member removed successfully.</div>";
}

// ---------------- EVENT CREATION ----------------
if (isset($_POST['create_event'])) {
    $event_name = trim($_POST['event_name']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location   = trim($_POST['location']);
    $roles      = $_POST['roles'] ?? [];
    $slots      = $_POST['slots'] ?? [];

    if (empty($event_name) || empty($event_date) || empty($event_time) || empty($location)) {
        $msg = "<div class='msg error'>⚠️ All required fields must be filled.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO events
            (event_name, description, event_date, event_time, location, created_by)
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $event_name, $description, $event_date, $event_time, $location, $email);

        if ($stmt->execute()) {
            $event_id = $conn->insert_id;

            // Insert roles
            for ($i = 0; $i < count($roles); $i++) {
                $role_name = trim($roles[$i]);
                $slot_num = intval($slots[$i]);
                if (!empty($role_name)) {
                    $r = $conn->prepare("INSERT INTO event_roles (event_id, role_name, slots) VALUES (?, ?, ?)");
                    $r->bind_param("isi", $event_id, $role_name, $slot_num);
                    $r->execute();
                }
            }

            $msg = "<div class='msg success'>✅ Event & crew roles created successfully.</div>";
        } else {
            $msg = "<div class='msg error'>❌ Failed to create event.</div>";
        }
    }
}

// ---------------- JOIN REQUEST ACTIONS ----------------
if (isset($_GET['req_approve'])) {
    $req_id = intval($_GET['req_approve']);
    // Get request info
    $info = $conn->query("SELECT event_id, role_id FROM event_requests WHERE id=$req_id")->fetch_assoc();
    $event_id = $info['event_id'];
    $role_id  = $info['role_id'];

    // Approve request
    $conn->query("UPDATE event_requests SET status='approved' WHERE id=$req_id");

    // Decrease available slot count
    $conn->query("UPDATE event_roles SET slots = slots - 1 WHERE id=$role_id AND slots > 0");

    $msg = "<div class='msg success'>✅ Join request approved.</div>";
}
if (isset($_GET['req_reject'])) {
    $req_id = intval($_GET['req_reject']);
    $conn->query("UPDATE event_requests SET status='rejected' WHERE id=$req_id");
    $msg = "<div class='msg error'>❌ Join request rejected.</div>";
}

// ---------------- MARK EVENT AS FINISHED ----------------
if (isset($_GET['finish_event'])) {
    $eid = intval($_GET['finish_event']);
    $conn->query("UPDATE events SET status='finished' WHERE id=$eid");
    $msg = "<div class='msg success'>✅ Event marked as finished.</div>";
}

// ---------------- FETCH DATA ----------------
$pending  = $conn->query("SELECT * FROM users WHERE role='student' AND status='pending'");
$approved = $conn->query("SELECT * FROM users WHERE role='member' AND status='approved'");
$events   = $conn->query("SELECT * FROM events WHERE status='ongoing' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GPSphere | Admin Dashboard</title>
<style>
body{font-family:Arial;background:#f4f6f7;margin:0;padding:30px;}
h1,h2{color:#2c3e50;}
nav{margin-bottom:20px;}
nav button{background:#2980b9;color:white;border:none;padding:10px 20px;border-radius:5px;cursor:pointer;margin-right:10px;}
nav button.active{background:#1c5980;}
section{display:none;}
section.active{display:block;}
table{border-collapse:collapse;width:95%;background:#fff;margin-top:15px;border-radius:8px;overflow:hidden;box-shadow:0 4px 8px rgba(0,0,0,0.1);}
th,td{padding:10px 12px;border-bottom:1px solid #eee;}
th{background:#2980b9;color:#fff;}
.msg{margin:15px 0;padding:10px;border-radius:6px;width:60%;}
.success{background:#d4edda;color:#155724;}
.error{background:#f8d7da;color:#721c24;}
a.btn{text-decoration:none;padding:6px 10px;border-radius:5px;color:#fff;font-size:13px;}
.approve{background:#27ae60;}
.reject{background:#e74c3c;}
.remove{background:#c0392b;}
.finish{background:#f39c12;}
.logout{background:#34495e;color:white;padding:8px 15px;border-radius:5px;text-decoration:none;}
.logout:hover{background:#2c3e50;}
form{background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,0.1);width:60%;margin-top:15px;}
input,textarea{width:100%;padding:8px;margin:8px 0;border:1px solid #ccc;border-radius:6px;}
button[type=submit]{background:#2980b9;color:white;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;}
button[type=submit]:hover{background:#1c5980;}
.roleRow{display:flex;gap:10px;margin-bottom:8px;}
.roleRow input{flex:1;}
.removeBtn{background:#e74c3c;color:white;border:none;border-radius:4px;cursor:pointer;padding:5px 10px;}
.removeBtn:hover{background:#c0392b;}
#addRoleBtn{background:#27ae60;color:white;border:none;padding:6px 12px;border-radius:5px;cursor:pointer;}
#addRoleBtn:hover{background:#219150;}
ul.member-list{margin:5px 0;padding-left:20px;}
ul.member-list li{font-size:14px;color:#2c3e50;}
</style>
</head>

<body>
<h1>Admin Dashboard</h1>
<p>Welcome, <b><?= $name; ?></b> 👋 | <a href="logout.php" class="logout">Logout</a></p>
<?= $msg; ?>

<nav>
  <button id="btnMembers" class="active">👥 Members</button>
  <button id="btnEvents">📅 Events</button>
</nav>

<!-- MEMBERS SECTION -->
<section id="membersSection" class="active">
  <h2>Pending Approvals</h2>
  <table>
    <tr><th>Name</th><th>Email</th><th>Action</th></tr>
    <?php if($pending->num_rows>0): while($r=$pending->fetch_assoc()): ?>
      <tr>
        <td><?= $r['name']; ?></td>
        <td><?= $r['email']; ?></td>
        <td><a href="?approve=<?= $r['id']; ?>" class="btn approve">Approve</a> <a href="?reject=<?= $r['id']; ?>" class="btn reject">Reject</a></td>
      </tr>
    <?php endwhile; else: ?><tr><td colspan="3">No pending students.</td></tr><?php endif; ?>
  </table>

  <h2>Approved Members</h2>
  <table>
    <tr><th>Name</th><th>Email</th><th>Action</th></tr>
    <?php if($approved->num_rows>0): while($r=$approved->fetch_assoc()): ?>
      <tr>
        <td><?= isset($r['name']) ? $r['name'] : (isset($r['NAME']) ? $r['NAME'] : ''); ?></td>
        <td><?= $r['email']; ?></td>
        <td><a href="?remove=<?= $r['id']; ?>" class="btn remove">Remove</a></td>
      </tr>
    <?php endwhile; else: ?><tr><td colspan="3">No members found.</td></tr><?php endif; ?>
  </table>
</section>

<!-- EVENTS SECTION -->
<section id="eventsSection">
  <h2>Create New Event</h2>
  <form method="POST" action="">
    <input type="text" name="event_name" placeholder="Event Name" required>
    <textarea name="description" placeholder="Event Description"></textarea>
    <input type="date" name="event_date" required>
    <input type="time" name="event_time" required>
    <input type="text" name="location" placeholder="Location" required>
    <h3>Crew Roles</h3>
    <div id="rolesContainer">
      <div class="roleRow">
        <input type="text" name="roles[]" placeholder="Role Name (e.g. Director)" required>
        <input type="number" name="slots[]" placeholder="Slots" min="1" value="1" required>
        <button type="button" class="removeBtn" onclick="removeRole(this)">❌</button>
      </div>
    </div>
    <button type="button" id="addRoleBtn">➕ Add Role</button>
    <br><br><button type="submit" name="create_event">Create Event</button>
  </form>

  <h2>Ongoing Events</h2>
  <table>
    <tr><th>Event</th><th>Date</th><th>Time</th><th>Location</th><th>Members</th><th>Join Requests</th><th>Action</th></tr>
    <?php if($events->num_rows>0): while($e=$events->fetch_assoc()): ?>
      <tr>
        <td><b><?= $e['event_name']; ?></b></td>
        <td><?= $e['event_date']; ?></td>
        <td><?= $e['event_time']; ?></td>
        <td><?= $e['location']; ?></td>
        <td>
          <?php
          $joined = $conn->query("
            SELECT u.name, r.role_name FROM event_requests er
            JOIN users u ON er.user_id=u.id
            JOIN event_roles r ON er.role_id=r.id
            WHERE er.event_id={$e['id']} AND er.status='approved'
          ");
          if($joined->num_rows>0){
            echo "<ul class='member-list'>";
            while($j=$joined->fetch_assoc()){
              echo "<li>{$j['name']} – {$j['role_name']}</li>";
            }
            echo "</ul>";
          } else echo "<i>No members joined yet.</i>";
          ?>
        </td>
        <td>
          <?php
          $pendingReqs = $conn->query("
            SELECT er.id, u.name, r.role_name FROM event_requests er
            JOIN users u ON er.user_id=u.id
            JOIN event_roles r ON er.role_id=r.id
            WHERE er.event_id={$e['id']} AND er.status='pending'
          ");
          if($pendingReqs->num_rows>0){
            while($req=$pendingReqs->fetch_assoc()){
              echo "<div>{$req['name']} ({$req['role_name']})
              <a href='?req_approve={$req['id']}' class='btn approve'>Approve</a>
              <a href='?req_reject={$req['id']}' class='btn reject'>Reject</a></div>";
            }
          } else echo "<i>No pending requests.</i>";
          ?>
        </td>
        <td><a href="?finish_event=<?= $e['id']; ?>" class="btn finish">Finish</a></td>
      </tr>
    <?php endwhile; else: ?><tr><td colspan="7">No ongoing events.</td></tr><?php endif; ?>
  </table>
</section>

<script>
const btnMembers=document.getElementById('btnMembers');
const btnEvents=document.getElementById('btnEvents');
const sections={members:document.getElementById('membersSection'),events:document.getElementById('eventsSection')};
function activate(tab){
  btnMembers.classList.remove('active');btnEvents.classList.remove('active');
  sections.members.classList.remove('active');sections.events.classList.remove('active');
  if(tab==='members'){btnMembers.classList.add('active');sections.members.classList.add('active');}
  if(tab==='events'){btnEvents.classList.add('active');sections.events.classList.add('active');}
}
btnMembers.onclick=()=>activate('members');
btnEvents.onclick=()=>activate('events');

const rolesContainer=document.getElementById('rolesContainer');
const addRoleBtn=document.getElementById('addRoleBtn');
addRoleBtn.addEventListener('click',()=>{
  const div=document.createElement('div');
  div.classList.add('roleRow');
  div.innerHTML=`<input type="text" name="roles[]" placeholder="Role Name" required>
                 <input type="number" name="slots[]" placeholder="Slots" min="1" value="1" required>
                 <button type="button" class="removeBtn" onclick="removeRole(this)">❌</button>`;
  rolesContainer.appendChild(div);
});
function removeRole(btn){btn.parentElement.remove();}
</script>
</body>
</html>
