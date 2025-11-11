<?php
// chatbot.php - Floating widget with quick buttons
?>
<link rel="stylesheet" href="chatbot.css">

<div id="chatbot-container">
  <button id="chatbot-toggle" title="Open chat">💬</button>

  <div id="chatbot-box" role="dialog" aria-label="GPSphere Assistant">
    <div id="chatbot-header">GPSphere Assistant 🤖</div>

    <div id="chatbot-messages">
      <div class="bot-msg">
        Hello! 👋 I’m the GPSphere Assistant.<br>
        Ask me about GPSphere, registration, or available events!
      </div>
    </div>

    <div id="chatbot-quick">
      <button class="quick-btn">What is GPS</button>
      <button class="quick-btn">How to register</button>
      <button class="quick-btn">What events available</button>
      <button class="quick-btn">How to join</button>
    </div>
  </div>
</div>

<script src="chatbot.js"></script>
