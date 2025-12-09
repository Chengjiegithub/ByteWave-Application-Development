This document summarizes all important changes made during the Node.js migration.

---

# 📘 README (CJ CHANGES)

*Last updated: 2025-11-27*

This document summarises all important changes made during the Node.js migration.
Please read this before continuing development.

---

## **1. Major System Upgrade**

The system has been upgraded from the old `index.html` prototype to a **complete Node.js backend + multi-dashboard frontend**.

### New system includes:

* Express.js backend
* MySQL database (users, events, roles, applications)
* Admin, Member, and Student dashboards
* TAC-based login system
* Floating FAQ-style chatbot
* Cleaned and correct folder structure

---

## **2. Folder Structure Fix (IMPORTANT)**

The previous version had:

* ❌ `package.json` inside `/public`
* ❌ `node_modules` inside `/public`

This caused:

* npm not working
* backend errors
* node_modules exposed publicly
* express static folder conflicts

### ✔ Fixed Structure

```
/nodejs
   package.json
   node_modules/
   server.js
   src/
       controllers/
       routes/
       utils/
       config/
   public/
       *.html
       chatbot.js
       chatbot_widget.html
       assets/
```

---

## **3. Database Port Reminder**

Sou is using:

```
DB_PORT=3307
```

Others use default:

```
DB_PORT=3306
```

⚠️ **Teammates must adjust `.env` to match their MySQL port.**

If login or events fail → it is usually a DB port mismatch.

---

## **4. TAC Test Mode**

To prevent spam Gmail sending during development:

### ✔ Test Mode (Recommended for development)

```
TAC_TEST_MODE=true
```

**Behavior:**

* TAC appears in console
* TAC returned in login response
* No real email sent

### ✔ Real Email Mode

```
TAC_TEST_MODE=false
```

**Behavior:**

* Sends TAC via Gmail SMTP
* TAC not returned to frontend

⚠️ Turn Test Mode OFF when demonstrating or testing Gmail sending.

---

## **5. Backend Updates**

### Authentication

* Full password + TAC login flow
* TAC expiry added
* Fixed missing `await` (previously always sent real emails)

### User Management

* Admin approval now sets:

  * `status = "approved"`
  * `role = "member"`

### Event System

* Event creation + roles
* Member apply for role
* Member cancel pending applications
* Admin approve/reject applications
* Added approvedCount / pendingCount
* Added endpoint for approved crew list
* Fixed event name not appearing in member dashboard
* Fixed “roles is not defined” error

---

## **6. Frontend Updates**

### Homepage

* Clean UI
* Chatbot enabled

### Login/Register

* TAC login system
* Chatbot hidden

### Student Dashboard

* Shows approval status
* Auto-redirects when approved

### Member Dashboard

* Event list with roles
* Slot counts
* Apply / Cancel application
* Shows Approved / Pending / Rejected
* Displays Approved Crew list
* Displays “Your Applications” section

### Admin Dashboard

* Event creation with multiple roles
* View applications in modal
* Approve/Reject workflow
* Clean event layout

---

## **7. Chatbot Integration**

A floating FAQ-style chatbot has been added.

### Appears on:

* homepage
* student_dashboard
* member_dashboard

### Hidden on:

* login_register
* admin_dashboard

### Files added:

```
/public/chatbot.js
/public/chatbot_widget.html
```

---

## **8. After Pulling — Setup Steps**

### 1) Install dependencies

```
cd nodejs
npm install
```

### 2) Update `.env`

```
DB_PORT=3307   # or 3306 depending on your MySQL
TAC_TEST_MODE=true   # recommended for development
```

### 3) (Optional) Recreate DB tables

```
node initDb.js
```

### 4) Start backend

```
npm run dev
```

---

## **9. Team To-Do(Recommendation)**

* UI enhancement & polishing
* Mobile responsiveness
* Admin analytics
* Add more chatbot topics
* Error handling improvements
* Improve documentation

---

# ✅ End of README (CJ CHANGES)
