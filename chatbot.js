(function () {
  const toggleBtn = document.getElementById('chatbot-toggle');
  const chatbotBox = document.getElementById('chatbot-box');
  const chatMessages = document.getElementById('chatbot-messages');
  const quickBtns = document.querySelectorAll('.quick-btn');

  // Detect base path and backend URL
  const base = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/');
  const backend = base + 'chatbot_response.php';

  toggleBtn.addEventListener('click', () => {
    chatbotBox.style.display = chatbotBox.style.display === 'flex' ? 'none' : 'flex';
  });

  quickBtns.forEach(b => {
    b.addEventListener('click', () => {
      const text = b.innerText.trim();
      sendMessage(text);
    });
  });

  function appendMessage(text, type = 'bot') {
    const div = document.createElement('div');
    div.className = type === 'bot' ? 'bot-msg' : 'user-msg';
    div.innerHTML = text;
    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function sendMessage(text) {
    if (text === '') return;
    appendMessage(text, 'user');

    fetch(backend, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: 'message=' + encodeURIComponent(text)
    })
      .then(async res => {
        // ✅ This version handles extra text before JSON safely
        const raw = await res.text();
        const match = raw.match(/\{[\s\S]*\}/); // Find JSON block
        let replyText = "⚠️ Sorry, there was a problem retrieving information.";

        if (match) {
          try {
            const data = JSON.parse(match[0]);
            if (data.reply) replyText = data.reply;
          } catch (err) {
            console.warn("JSON parse error:", err, raw);
          }
        } else {
          console.warn("No JSON detected:", raw);
        }

        appendMessage(replyText, 'bot');
      })
      .catch(err => {
        console.error("Fetch error:", err);
        appendMessage("⚠️ Sorry, I couldn't reach the assistant.", 'bot');
      });
  }
})();
