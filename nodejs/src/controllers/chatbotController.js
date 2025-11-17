// ============================================
// 📋 STEP 13: CHATBOT CONTROLLER
// ============================================
// Replaces chatbot.php
// Simple chatbot API that responds to user queries

const getChatbotResponse = async (req, res) => {
  try {
    const { message } = req.body;

    if (!message) {
      return res.status(400).json({ error: 'Message required' });
    }

    const lowerMessage = message.toLowerCase();
    let response = '';

    // Simple chatbot logic (can be enhanced with AI)
    if (lowerMessage.includes('gps') || lowerMessage.includes('what is')) {
      response = '🌍 GPSphere is a student organization focused on geospatial technology and mapping. We organize workshops, competitions, and networking events!';
    } 
    else if (lowerMessage.includes('register') || lowerMessage.includes('how to register')) {
      response = '📝 To register:\n1. Go to the registration page\n2. Fill in your details\n3. Create a strong password\n4. Wait for admin approval\n5. You\'ll receive an email when approved!';
    }
    else if (lowerMessage.includes('event') || lowerMessage.includes('available')) {
      response = '📅 Check our events page to see all upcoming events. Events include workshops, competitions, and social gatherings!';
    }
    else if (lowerMessage.includes('join') || lowerMessage.includes('how to join')) {
      response = '👥 To join:\n1. Register an account\n2. Wait for approval\n3. Browse available events\n4. Sign up for events you\'re interested in!';
    }
    else if (lowerMessage.includes('contact') || lowerMessage.includes('help')) {
      response = '📞 You can contact us through:\n- Email: info@gpsphere.com\n- Contact form on website\n- Social media pages';
    }
    else {
      response = '🤖 I\'m the GPSphere Assistant! Ask me about:\n- What is GPS/GPSphere\n- How to register\n- Available events\n- How to join\n- Contact information';
    }

    return res.json({ 
      reply: response,
      timestamp: new Date()
    });
  } catch (error) {
    console.error('Chatbot error:', error);
    return res.status(500).json({ error: 'Failed to process message' });
  }
};

module.exports = {
  getChatbotResponse
};
