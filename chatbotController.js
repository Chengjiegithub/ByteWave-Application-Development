// ============================================
// 📋 AI CHATBOT CONTROLLER
// ============================================
// Enhanced chatbot with AI-like responses and conversation handling

const pool = require('../config/database');

const getChatbotResponse = async (req, res) => {
  try {
    const { message, conversationId } = req.body;

    if (!message || !message.trim()) {
      return res.status(400).json({ error: 'Message required' });
    }

    const lowerMessage = message.toLowerCase().trim();
    let response = '';
    let suggestions = [];

    // Greetings and casual conversation
    if (lowerMessage.match(/^(hi|hello|hey|greetings|good morning|good afternoon|good evening)/)) {
      response = '👋 Hello! I\'m the GPS UTM Assistant. How can I help you today?';
      suggestions = ['What is GPS UTM?', 'How do I register?', 'Tell me about events'];
    }
    // GPS UTM Information
    else if (lowerMessage.includes('gps') || lowerMessage.includes('what is gps') || lowerMessage.includes('gps utm') || lowerMessage.includes('gerakan pengguna siswa')) {
      response = '🌍 **GPS UTM** (Gerakan Pengguna Siswa) is the Student Consumer Movement at Universiti Teknologi Malaysia.\n\n' +
                 'We empower students to become smart, ethical, and responsible consumers through:\n' +
                 '• Educational workshops\n' +
                 '• Consumer rights awareness\n' +
                 '• Community events\n' +
                 '• Student advocacy\n\n' +
                 'GPSphere is our digital platform for managing members, events, and activities!';
      suggestions = ['How do I join?', 'What events are available?', 'How do I register?'];
    }
    // Registration
    else if (lowerMessage.includes('register') || lowerMessage.includes('sign up') || lowerMessage.includes('create account') || lowerMessage.includes('how to register')) {
      response = '📝 **Registration Process:**\n\n' +
                 '1. Click on "Register" or go to the registration page\n' +
                 '2. Fill in your details (name, email, password)\n' +
                 '3. Make sure your password is strong (8+ characters, uppercase, lowercase, number, symbol)\n' +
                 '4. Submit your registration\n' +
                 '5. Wait for admin approval (usually 1-2 business days)\n' +
                 '6. You\'ll receive an email notification once approved!\n\n' +
                 'Once approved, you\'ll become a GPS member and can participate in events!';
      suggestions = ['What is TAC?', 'How do I login?', 'What happens after registration?'];
    }
    // Login and TAC
    else if (lowerMessage.includes('login') || lowerMessage.includes('sign in') || lowerMessage.includes('tac') || lowerMessage.includes('authentication code')) {
      response = '🔐 **Login & TAC System:**\n\n' +
                 '**TAC** stands for "Time Authentication Code" - it\'s a 6-digit security code sent to your email.\n\n' +
                 '**Login Steps:**\n' +
                 '1. Enter your email and password\n' +
                 '2. Click "Login"\n' +
                 '3. Check your email for the TAC code\n' +
                 '4. Enter the TAC code (expires in 15 minutes)\n' +
                 '5. You\'re in! 🎉\n\n' +
                 '**Note:** In test mode, the TAC appears on screen instead of email.';
      suggestions = ['I didn\'t receive TAC', 'Forgot password', 'How to change password?'];
    }
    // Events
    else if (lowerMessage.includes('event') || lowerMessage.includes('activities') || lowerMessage.includes('what events') || lowerMessage.includes('upcoming')) {
      // Try to fetch actual events from database
      try {
        const [events] = await pool.query(
          "SELECT event_name, description, event_date, location FROM events WHERE status = 'ongoing' ORDER BY event_date ASC LIMIT 5"
        );
        
        if (events.length > 0) {
          response = '📅 **Upcoming Events:**\n\n';
          events.forEach((event, index) => {
            const date = event.event_date ? new Date(event.event_date).toLocaleDateString() : 'TBA';
            response += `${index + 1}. **${event.event_name}**\n`;
            if (event.description) response += `   ${event.description.substring(0, 100)}...\n`;
            response += `   📍 ${event.location || 'TBA'}\n`;
            response += `   📆 ${date}\n\n`;
          });
          response += 'Visit your dashboard to see all events and apply for roles!';
        } else {
          response = '📅 Currently, there are no upcoming events scheduled.\n\n' +
                     'Check back later or visit your dashboard to see when new events are posted!\n\n' +
                     'Events typically include:\n' +
                     '• Workshops and training sessions\n' +
                     '• Consumer awareness campaigns\n' +
                     '• Community service activities\n' +
                     '• Networking events';
        }
      } catch (err) {
        response = '📅 You can view all available events on your dashboard after logging in!\n\n' +
                   'Events include workshops, competitions, and community activities.';
      }
      suggestions = ['How do I join an event?', 'What roles are available?', 'How to apply?'];
    }
    // Joining events
    else if (lowerMessage.includes('join') || lowerMessage.includes('apply') || lowerMessage.includes('participate') || lowerMessage.includes('how to join event')) {
      response = '🎉 **How to Join an Event:**\n\n' +
                 '1. **Login** to your member dashboard\n' +
                 '2. **Browse** available events\n' +
                 '3. **Select** an event you\'re interested in\n' +
                 '4. **Choose** a role (Director, Helper, Technical Crew, etc.)\n' +
                 '5. **Click** "Apply" on your desired role\n' +
                 '6. **Wait** for admin approval\n' +
                 '7. You\'ll be notified once approved! ✅\n\n' +
                 '**Note:** You must be an approved member to join events.';
      suggestions = ['What roles are available?', 'How long does approval take?', 'Can I apply for multiple roles?'];
    }
    // Roles
    else if (lowerMessage.includes('role') || lowerMessage.includes('position') || lowerMessage.includes('crew') || lowerMessage.includes('director') || lowerMessage.includes('helper')) {
      response = '💼 **Available Event Roles:**\n\n' +
                 'Events typically have various roles you can apply for:\n\n' +
                 '• **Director** - Overall event coordination\n' +
                 '• **Secretary** - Documentation and administration\n' +
                 '• **Technical Crew** - Setup and technical support\n' +
                 '• **Helper** - General assistance and support\n' +
                 '• **Publicity** - Marketing and promotion\n\n' +
                 'Each event may have different roles and requirements. Check the event details for specific roles available!';
      suggestions = ['How do I apply?', 'What are the requirements?', 'Can I apply for multiple roles?'];
    }
    // Contact
    else if (lowerMessage.includes('contact') || lowerMessage.includes('help') || lowerMessage.includes('support') || lowerMessage.includes('email') || lowerMessage.includes('phone')) {
      response = '📞 **Contact GPS UTM:**\n\n' +
                 'You can reach us through:\n\n' +
                 '• **Email:** info@gpsphere.com\n' +
                 '• **GPS Room:** Visit the GPS office at UTM\n' +
                 '• **Social Media:** Follow our official pages\n' +
                 '• **Website:** Use the contact form\n\n' +
                 'For urgent matters, please visit the GPS room during office hours.';
      suggestions = ['Where is the GPS room?', 'What are office hours?', 'How to report an issue?'];
    }
    // Status/Approval
    else if (lowerMessage.includes('status') || lowerMessage.includes('approval') || lowerMessage.includes('pending') || lowerMessage.includes('approved') || lowerMessage.includes('when will')) {
      response = '⏳ **Account Status:**\n\n' +
                 'After registration, your account status is "Pending" until an admin reviews and approves it.\n\n' +
                 '**Timeline:**\n' +
                 '• Registration submitted → Pending\n' +
                 '• Admin review → Usually 1-2 business days\n' +
                 '• Approval → You become a member! ✅\n\n' +
                 'You can check your status on your dashboard. You\'ll also receive an email notification when approved!';
      suggestions = ['How long does it take?', 'What if I\'m rejected?', 'How to check status?'];
    }
    // Password
    else if (lowerMessage.includes('password') || lowerMessage.includes('forgot password') || lowerMessage.includes('reset password')) {
      response = '🔑 **Password Help:**\n\n' +
                 '**Password Requirements:**\n' +
                 '• At least 8 characters\n' +
                 '• One uppercase letter\n' +
                 '• One lowercase letter\n' +
                 '• One number\n' +
                 '• One special symbol\n\n' +
                 '**If you forgot your password:**\n' +
                 'Please contact the admin or visit the GPS room for password reset assistance.';
      suggestions = ['How to change password?', 'Contact admin', 'What if I\'m locked out?'];
    }
    // Thank you
    else if (lowerMessage.match(/^(thanks|thank you|ty|appreciate|grateful)/)) {
      response = '😊 You\'re welcome! Is there anything else I can help you with?';
      suggestions = ['Tell me about events', 'How to register?', 'Contact information'];
    }
    // Goodbye
    else if (lowerMessage.match(/^(bye|goodbye|see you|farewell|exit|quit)/)) {
      response = '👋 Goodbye! Feel free to come back if you have any questions. Have a great day!';
      suggestions = [];
    }
    // Default - AI-like response
    else {
      // Try to understand context and provide helpful response
      if (lowerMessage.includes('how') || lowerMessage.includes('what') || lowerMessage.includes('when') || lowerMessage.includes('where') || lowerMessage.includes('why')) {
        response = '🤔 I understand you\'re asking about something. Let me help you!\n\n' +
                   'I can assist you with:\n' +
                   '• GPS UTM information\n' +
                   '• Registration process\n' +
                   '• Login and TAC\n' +
                   '• Events and activities\n' +
                   '• Joining events\n' +
                   '• Contact information\n\n' +
                   'Could you rephrase your question or try one of the suggestions below?';
      } else {
        response = '🤖 I\'m the GPS UTM Assistant! I can help you with:\n\n' +
                   '📌 **Information:**\n' +
                   '• What is GPS UTM?\n' +
                   '• How to register\n' +
                   '• Login and TAC system\n\n' +
                   '📅 **Events:**\n' +
                   '• Available events\n' +
                   '• How to join events\n' +
                   '• Event roles\n\n' +
                   '💬 **Support:**\n' +
                   '• Contact information\n' +
                   '• Account status\n' +
                   '• Password help\n\n' +
                   'What would you like to know?';
      }
      suggestions = ['What is GPS UTM?', 'How do I register?', 'Tell me about events', 'Contact information'];
    }

    // Add a small delay to simulate AI thinking (optional)
    await new Promise(resolve => setTimeout(resolve, 300 + Math.random() * 500));

    return res.json({ 
      reply: response,
      suggestions: suggestions,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    console.error('Chatbot error:', error);
    return res.status(500).json({ 
      error: 'Sorry, I encountered an error. Please try again or contact support.',
      reply: '❌ I\'m having trouble processing that. Could you try rephrasing your question?'
    });
  }
};

module.exports = {
  getChatbotResponse
};
