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
            if (!empty($roles) && !empty($slots)) {
                for ($i = 0; $i < count($roles); $i++) {
                    $role_name = trim($roles[$i]);
                    $slot_num  = intval($slots[$i]);
                    if (!empty($role_name)) {
                        $r = $conn->prepare("INSERT INTO event_roles (event_id, role_name, slots) VALUES (?, ?, ?)");
                        $r->bind_param("isi", $event_id, $role_name, $slot_num);
                        $r->execute();
                    }
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
    $id = intval($_GET['req_approve']);
    $conn->query("UPDATE event_requests SET status='approved' WHERE id=$id");
    $msg = "<div class='msg success'>✅ Join request approved.</div>";
}
if (isset($_GET['req_reject'])) {
    $id = intval($_GET['req_reject']);
    $conn->query("UPDATE event_requests SET status='rejected' WHERE id=$id");
    $msg = "<div class='msg error'>❌ Join request rejected.</div>";
}

// ---------------- FETCH DATA ----------------
$pending  = $conn->query("SELECT * FROM users WHERE role='student' AND status='pending'");
$approved = $conn->query("SELECT * FROM users WHERE role='member' AND status='approved'");
$events   = $conn->query("SELECT * FROM events ORDER BY created_at DESC");
$requests = $conn->query("
    SELECT er.id, e.event_name, u.name AS user, r.role_name, er.status
    FROM event_requests er
    JOIN users u ON er.user_id=u.id
    JOIN events e ON er.event_id=e.id
    JOIN event_roles r ON er.role_id=r.id
    ORDER BY er.requested_at DESC
");
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
nav button{
  background:#2980b9;color:white;border:none;padding:10px 20px;
  border-radius:5px;cursor:pointer;margin-right:10px;
}
nav button.active{background:#1c5980;}
section{display:none;}
section.active{display:block;}
table{
  border-collapse:collapse;width:95%;background:#fff;margin-top:15px;
  border-radius:8px;overflow:hidden;box-shadow:0 4px 8px rgba(0,0,0,0.1);
}
th,td{padding:10px 12px;border-bottom:1px solid #eee;}
th{background:#2980b9;color:#fff;}
.msg{margin:15px 0;padding:10px;border-radius:6px;width:60%;}
.success{background:#d4edda;color:#155724;}
.error{background:#f8d7da;color:#721c24;}
a.btn{text-decoration:none;padding:6px 10px;border-radius:5px;color:#fff;font-size:13px;}
.approve{background:#27ae60;}
.reject{background:#e74c3c;}
.remove{background:#c0392b;}
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
</style>
</head>

<body>
<h1>Admin Dashboard</h1>
<p>Welcome, <b><?= $name; ?></b> 👋 | <a href="logout.php" class="logout">Logout</a></p>
<?= $msg; ?>

<nav>
  <button id="btnMembers" class="active">👥 Members</button>
  <button id="btnEvents">📅 Events</button>
  <button id="btnRequests">📬 Join Requests</button>
</nav>

<!-- ================= MEMBERS SECTION ================= -->
<section id="membersSection" class="active">
  <h2>Pending Approvals</h2>
  <table>
  <tr><th>Name</th><th>Email</th><th>Action</th></tr>
  <?php if($pending->num_rows>0): while($r=$pending->fetch_assoc()): ?>
    <tr>
      <td><?= $r['name']??$r['NAME']; ?></td>
      <td><?= $r['email']??$r['EMAIL']; ?></td>
      <td>
        <a href="?approve=<?= $r['id']; ?>" class="btn approve">✅ Approve</a>
        <a href="?reject=<?= $r['id']; ?>" class="btn reject">❌ Reject</a>
      </td>
    </tr>
  <?php endwhile; else: ?><tr><td colspan="3">No pending applications.</td></tr><?php endif; ?>
  </table>

  <h2>Approved Members</h2>
  <table>
  <tr><th>Name</th><th>Email</th><th>Action</th></tr>
  <?php if($approved->num_rows>0): while($r=$approved->fetch_assoc()): ?>
    <tr>
      <td><?= $r['name']??$r['NAME']; ?></td>
      <td><?= $r['email']??$r['EMAIL']; ?></td>
      <td><a href="?remove=<?= $r['id']; ?>" class="btn remove">🗑 Remove</a></td>
    </tr>
  <?php endwhile; else: ?><tr><td colspan="3">No members yet.</td></tr><?php endif; ?>
  </table>
</section>

<!-- ================= EVENTS SECTION ================= -->
<section id="eventsSection">
  <h2>Create New Event</h2>
  <form method="POST" action="">
    <input type="text" name="event_name" placeholder="Event Name" required>
    <textarea name="description" placeholder="Event Description"></textarea>
    <input type="date" name="event_date" required>
    <input type="time" name="event_time" required>
    <input type="text" name="location" placeholder="Location" required>

    <h3>Event Crew Roles</h3>
    <div id="rolesContainer">
      <div class="roleRow">
        <input type="text" name="roles[]" placeholder="Role Name (e.g., Director)" required>
        <input type="number" name="slots[]" placeholder="Slots" min="1" value="1" required>
        <button type="button" class="removeBtn" onclick="removeRole(this)">❌</button>
      </div>
    </div>
    <button type="button" id="addRoleBtn">➕ Add Another Role</button>
    <br><br>
    <button type="submit" name="create_event">Create Event</button>
  </form>

  <h2>Existing Events</h2>
  <table>
  <tr><th>Event & Roles</th><th>Date</th><th>Time</th><th>Location</th><th>Created By</th></tr>
  <?php if($events->num_rows>0): while($e=$events->fetch_assoc()): ?>
    <tr>
      <td><b><?= htmlspecialchars($e['event_name']); ?></b>
        <?php
        $roles = $conn->query("SELECT role_name, slots FROM event_roles WHERE event_id={$e['id']}");
        if($roles->num_rows>0){
          echo "<ul style='margin:5px 0;padding-left:20px;'>";
          while($r=$roles->fetch_assoc()){
            echo "<li>{$r['role_name']} ({$r['slots']} slots)</li>";
          }
          echo "</ul>";
        }
        ?>
      </td>
      <td><?= $e['event_date']; ?></td>
      <td><?= $e['event_time']; ?></td>
      <td><?= $e['location']; ?></td>
      <td><?= $e['created_by']; ?></td>
    </tr>
  <?php endwhile; else: ?><tr><td colspan="5">No events yet.</td></tr><?php endif; ?>
  </table>
</section>

<!-- ================= JOIN REQUESTS SECTION ================= -->
<section id="requestsSection">
  <h2>Event Join Requests</h2>
  <table>
  <tr><th>Event</th><th>Member</th><th>Role</th><th>Status</th><th>Action</th></tr>
  <?php if($requests->num_rows>0): while($r=$requests->fetch_assoc()): ?>
    <tr>
      <td><?= $r['event_name']; ?></td>
      <td><?= $r['user']; ?></td>
      <td><?= $r['role_name']; ?></td>
      <td><?= ucfirst($r['status']); ?></td>
      <td>
        <?php if($r['status']=='pending'): ?>
          <a href="?req_approve=<?= $r['id']; ?>" class="btn approve">Approve</a>
          <a href="?req_reject=<?= $r['id']; ?>" class="btn reject">Reject</a>
        <?php else: ?>—
        <?php endif; ?>
      </td>
    </tr>
  <?php endwhile; else: ?><tr><td colspan="5">No join requests yet.</td></tr><?php endif; ?>
  </table>
</section>

<script>
// Tab switching
const btnMembers=document.getElementById('btnMembers');
const btnEvents=document.getElementById('btnEvents');
const btnRequests=document.getElementById('btnRequests');
const sections={
  members:document.getElementById('membersSection'),
  events:document.getElementById('eventsSection'),
  requests:document.getElementById('requestsSection')
};
function activate(tab){
  btnMembers.classList.remove('active');
  btnEvents.classList.remove('active');
  btnRequests.classList.remove('active');
  sections.members.classList.remove('active');
  sections.events.classList.remove('active');
  sections.requests.classList.remove('active');
  if(tab==='members'){btnMembers.classList.add('active');sections.members.classList.add('active');}
  if(tab==='events'){btnEvents.classList.add('active');sections.events.classList.add('active');}
  if(tab==='requests'){btnRequests.classList.add('active');sections.requests.classList.add('active');}
}
btnMembers.onclick=()=>activate('members');
btnEvents.onclick=()=>activate('events');
btnRequests.onclick=()=>activate('requests');

// Add/remove role rows
const rolesContainer=document.getElementById('rolesContainer');
const addRoleBtn=document.getElementById('addRoleBtn');
addRoleBtn.addEventListener('click',()=>{
  const div=document.createElement('div');
  div.classList.add('roleRow');
  div.innerHTML=`
    <input type="text" name="roles[]" placeholder="Role Name (e.g., Vice Director)" required>
    <input type="number" name="slots[]" placeholder="Slots" min="1" value="1" required>
    <button type="button" class="removeBtn" onclick="removeRole(this)">❌</button>
  `;
  rolesContainer.appendChild(div);
});
function removeRole(btn){btn.parentElement.remove();}
</script>
</body>
</html>
