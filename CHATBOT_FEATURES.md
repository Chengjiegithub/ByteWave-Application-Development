# 🤖 AI Chatbot Features

## Overview

The GPS UTM chatbot has been upgraded to a fully functional AI assistant with an interactive chat interface.

## ✨ Key Features

### 1. **Interactive Chat Interface**
- Real-time messaging with message history
- User and bot message bubbles with distinct styling
- Smooth animations and transitions
- Auto-scrolling to latest messages

### 2. **AI-Like Responses**
- Natural language understanding
- Context-aware responses
- Multiple conversation topics supported
- Intelligent fallback responses

### 3. **Enhanced User Experience**
- Typing indicators while bot is "thinking"
- Suggestion buttons for quick responses
- Welcome message on first load
- Keyboard shortcuts (Enter to send, Escape to close)
- Responsive design

### 4. **Smart Features**
- Database integration (fetches real events)
- Conversation flow handling
- Error handling with user-friendly messages
- Timestamp display for messages

## 📋 Supported Topics

The chatbot can help with:

1. **GPS UTM Information**
   - What is GPS UTM?
   - Organization details
   - Mission and vision

2. **Registration**
   - How to register
   - Registration process
   - Account approval

3. **Login & Authentication**
   - TAC system explanation
   - Login process
   - Password help

4. **Events**
   - Available events (fetched from database)
   - Event details
   - Upcoming activities

5. **Event Participation**
   - How to join events
   - Role applications
   - Approval process

6. **Support**
   - Contact information
   - Account status
   - General help

7. **Casual Conversation**
   - Greetings
   - Thank you responses
   - Goodbye messages

## 🎨 UI Components

### Chat Widget
- **Floating Button**: Blue circular button with chat icon (bottom-right)
- **Chat Window**: 380px × 600px modal with rounded corners
- **Header**: Gradient blue header with bot icon
- **Messages Area**: Scrollable area with message bubbles
- **Input Area**: Text input with send button

### Message Bubbles
- **User Messages**: Blue gradient, right-aligned
- **Bot Messages**: White with border, left-aligned
- **Timestamps**: Small gray text below each message
- **Suggestions**: Clickable buttons below bot messages

### Typing Indicator
- Three animated dots
- Appears while waiting for bot response
- Auto-removes when response arrives

## 🔧 Technical Details

### Backend (`chatbotController.js`)
- Enhanced keyword matching with context awareness
- Database queries for real-time event data
- Natural conversation flow
- Error handling and fallbacks
- Response delay simulation (300-800ms) for AI-like feel

### Frontend (`chatbot_widget.html`)
- Vanilla JavaScript (no dependencies)
- Fetch API for backend communication
- Real-time message rendering
- Auto-scroll and focus management
- Keyboard event handling

### Integration (`chatbot.js`)
- Creates floating button
- Manages iframe display
- Toggle functionality
- Escape key support

## 📡 API Endpoint

### POST `/api/chatbot`

**Request:**
```json
{
  "message": "What is GPS UTM?"
}
```

**Response:**
```json
{
  "reply": "🌍 **GPS UTM** (Gerakan Pengguna Siswa)...",
  "suggestions": ["How do I join?", "What events are available?"],
  "timestamp": "2025-01-15T10:30:00.000Z"
}
```

## 🚀 Usage

1. **Access**: The chatbot appears on all pages with the floating button
2. **Open**: Click the blue chat button (bottom-right)
3. **Chat**: Type your message and press Enter or click Send
4. **Suggestions**: Click suggestion buttons for quick responses
5. **Close**: Click the button again or press Escape

## 🎯 Example Conversations

### Registration Help
```
User: How do I register?
Bot: 📝 **Registration Process:**
     1. Click on "Register"...
     [Suggestions: What is TAC?, How do I login?]
```

### Event Inquiry
```
User: What events are available?
Bot: 📅 **Upcoming Events:**
     1. **Workshop Name**
        Description...
        📍 Location
        📆 Date
     [Suggestions: How do I join an event?, What roles are available?]
```

### General Question
```
User: Hello
Bot: 👋 Hello! I'm the GPS UTM Assistant. How can I help you today?
     [Suggestions: What is GPS UTM?, How do I register?, Tell me about events]
```

## 🔮 Future Enhancements

Potential improvements:
- Conversation history persistence
- Multi-language support
- Voice input/output
- Integration with external AI APIs (OpenAI, etc.)
- Admin dashboard for training responses
- Analytics and conversation tracking

## 📝 Notes

- The chatbot uses pattern matching, not true AI/ML
- Responses are pre-programmed but context-aware
- Database integration provides real-time event data
- All responses are in English (can be extended)

---

**Last Updated**: January 2025
**Version**: 2.0 (AI-Enhanced)

