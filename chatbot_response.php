<?php
include('config.php');
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
header('Content-Type: application/json; charset=utf-8');

$input = strtolower(trim($_POST['message'] ?? ''));
$response = "";

if ($input === '') {
  echo json_encode(['reply' => "Please type something — e.g. 'What events available?'"]);
  exit;
}

try {
  if (strpos($input, 'gps') !== false) {
    $response = "GPSphere (Gerakan Pengguna Siswa) is a UTM student movement that promotes smart, ethical, and responsible consumerism.";
  } 
  elseif (strpos($input, 'register') !== false) {
    $response = "To register, go to the Registration page, fill out the form, and wait for admin approval.";
  } 
  elseif (strpos($input, 'join') !== false) {
    $response = "To join an event, go to your Member Dashboard → find the event → click 'Join' for the crew role you want.";
  } 
  elseif (strpos($input, 'event') !== false || strpos($input, 'available') !== false) {
    $sql = "SELECT event_name, event_date, event_time, location 
            FROM events WHERE status='ongoing' ORDER BY event_date ASC LIMIT 5";
    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
      $response = "Here are ongoing or upcoming events:<br><ul>";
      while ($r = $res->fetch_assoc()) {
        $response .= "<li><b>{$r['event_name']}</b> — " .
                     ($r['event_date'] ?: 'TBD') . " " .
                     ($r['event_time'] ?: '') . " at " .
                     ($r['location'] ?: 'TBD') . "</li>";
      }
      $response .= "</ul>";
    } else {
      $response = "There are currently no ongoing events. Please check back later.";
    }
  } 
  elseif (strpos($input, 'thank') !== false) {
    $response = "You're welcome! 😊";
  } 
  else {
    $response = "I’m not sure about that 🤔. You can ask about GPSphere, registration, or available events.";
  }

  echo json_encode(['reply' => $response]);
} catch (Exception $e) {
  echo json_encode(['reply' => "⚠️ Sorry, something went wrong on the server."]);
}
