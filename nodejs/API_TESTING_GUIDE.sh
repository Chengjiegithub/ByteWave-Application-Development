// ============================================
// 📋 QUICK START - TESTING THE API
// ============================================
// Copy & paste these examples to test in your terminal

// ============================================
// STEP 1: TEST IF SERVER IS RUNNING
// ============================================
curl http://localhost:3000/api/health

// Expected Response:
// {"status":"Server is running ✅"}

// ============================================
// STEP 2: REGISTER NEW USER
// ============================================
curl -X POST http://localhost:3000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "SecurePass123!",
    "confirm": "SecurePass123!"
  }'

// Expected Response:
// {"message":"Registration successful! Your account is pending admin approval."}

// ============================================
// STEP 3: ADMIN LOGIN TO APPROVE USERS
// Use email: admin@gpsphere.com (created during initDb.js)
// ============================================
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@gpsphere.com",
    "password": "Admin123!"
  }'

// Expected Response:
// {"message":"TAC code sent to your email. Please verify to complete login.","requiresTAC":true}

// Check your email for TAC code (or check console logs during development)

// ============================================
// STEP 4: VERIFY TAC CODE (Replace 123456 with actual TAC)
// ============================================
curl -X POST http://localhost:3000/api/auth/verify-tac \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@gpsphere.com",
    "tac_code": "123456"
  }'

// Expected Response:
// {
//   "message": "Login successful",
//   "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
//   "user": {
//     "id": 1,
//     "name": "System Admin",
//     "email": "admin@gpsphere.com",
//     "role": "admin"
//   }
// }

// ============================================
// STEP 5: GET USER PROFILE (Replace TOKEN with actual token)
// ============================================
curl http://localhost:3000/api/user/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

// Expected Response:
// {
//   "id": 1,
//   "name": "System Admin",
//   "email": "admin@gpsphere.com",
//   "role": "admin",
//   "status": "approved",
//   "created_at": "2025-01-16T10:30:00Z"
// }

// ============================================
// STEP 6: GET ALL USERS (Admin only)
// ============================================
curl http://localhost:3000/api/user/all \
  -H "Authorization: Bearer ADMIN_TOKEN"

// Expected Response:
// [
//   {"id": 1, "name": "System Admin", "email": "admin@gpsphere.com", ...},
//   {"id": 2, "name": "John Doe", "email": "john@example.com", ...}
// ]

// ============================================
// STEP 7: APPROVE PENDING USER (Admin only)
// ============================================
curl -X POST http://localhost:3000/api/user/approve \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"userId": 2}'

// Expected Response:
// {"message":"User approved successfully"}

// ============================================
// STEP 8: CREATE EVENT (Admin only)
// ============================================
curl -X POST http://localhost:3000/api/events \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "event_name": "GPS Workshop 2025",
    "description": "Learn GPS fundamentals and applications",
    "event_date": "2025-02-15",
    "event_time": "14:00:00",
    "location": "Room 101, Main Building",
    "director_needed": 1,
    "helper_needed": 5
  }'

// Expected Response:
// {
//   "message": "Event created successfully",
//   "eventId": 1
// }

// ============================================
// STEP 9: GET ALL EVENTS (Public)
// ============================================
curl http://localhost:3000/api/events

// Expected Response:
// [
//   {
//     "id": 1,
//     "event_name": "GPS Workshop 2025",
//     "description": "Learn GPS fundamentals and applications",
//     "event_date": "2025-02-15",
//     "event_time": "14:00:00",
//     "location": "Room 101, Main Building",
//     "status": "ongoing"
//   }
// ]

// ============================================
// STEP 10: GET EVENT DETAILS (Public)
// ============================================
curl http://localhost:3000/api/events/1

// Expected Response:
// {
//   "id": 1,
//   "event_name": "GPS Workshop 2025",
//   "roles": []
// }

// ============================================
// STEP 11: UPDATE EVENT (Admin only)
// ============================================
curl -X PUT http://localhost:3000/api/events/1 \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "event_name": "Advanced GPS Workshop 2025",
    "status": "finished"
  }'

// Expected Response:
// {"message":"Event updated successfully"}

// ============================================
// STEP 12: DELETE EVENT (Admin only)
// ============================================
curl -X DELETE http://localhost:3000/api/events/1 \
  -H "Authorization: Bearer ADMIN_TOKEN"

// Expected Response:
// {"message":"Event deleted successfully"}

// ============================================
// STEP 13: TEST CHATBOT (Public)
// ============================================
curl -X POST http://localhost:3000/api/chatbot \
  -H "Content-Type: application/json" \
  -d '{"message": "What is GPS?"}'

// Expected Response:
// {
//   "reply": "🌍 GPSphere is a student organization...",
//   "timestamp": "2025-01-16T10:30:00Z"
// }

// ============================================
// TIPS FOR TESTING
// ============================================
// 1. Use Postman (https://www.postman.com/download/) for easier testing
// 2. Or use Thunder Client VS Code extension
// 3. Copy-paste these commands in your Terminal
// 4. Replace token values with actual tokens from login response
// 5. Check browser console if using JavaScript
// 6. Check server console for error logs
