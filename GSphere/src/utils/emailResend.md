Perfect 👍
Below is the **adjusted, clean README section** that reflects **exactly what you are doing now**:

* ✅ Using **Resend**
* ✅ Keeping **your original HTML email styles**
* ✅ No test mode
* ✅ Render-ready
* ✅ Clear for teammates to follow

You can **copy–paste this directly** into your `README.md`.

---

## 📧 Email Service Migration (Nodemailer ➜ Resend)

This project has migrated all email sending from **Nodemailer (SMTP)** to **Resend (API-based)** to ensure reliable email delivery on **Render.com**.

Resend is used for:

* 🔐 TAC (Two-Factor Authentication) emails
* 🔒 Password reset emails
* 🎉 Welcome emails

### ✅ Why Resend Was Chosen

SMTP-based email services (Nodemailer + Gmail / Outlook / custom SMTP):

* ❌ Often fail or are blocked on Render
* ❌ Require open SMTP ports
* ❌ Can silently fail in production

Resend uses **HTTPS APIs**, which works reliably on Render and other cloud platforms.

---

## 🧹 STEP 1️⃣ Remove Old Email System (Nodemailer)

Uninstall Nodemailer:

```bash
npm uninstall nodemailer
```

Remove **all SMTP-related logic** from the codebase:

* `nodemailer`
* `createTransport(...)`
* `transporter.sendMail(...)`
* `TAC_TEST_MODE`
* Returning TAC / reset codes in API responses
* SMTP environment variables (`EMAIL_HOST`, `EMAIL_PORT`, `EMAIL_USER`, `EMAIL_PASS`)

---

## 🔑 STEP 2️⃣ Create a Resend Account

1. Go to [https://resend.com](https://resend.com)
2. Create an account
3. Verify your email or domain
4. Generate an **API Key**

---

## 🌍 STEP 3️⃣ Configure Environment Variables

Add the following variables to `.env` (local + Render):

```env
RESEND_API_KEY=your_resend_api_key_here
EMAIL_FROM="GPS UTM <no-reply@gpsutm.my>"
```

⚠️ **Important**
`EMAIL_FROM` must be a **verified email or domain** in Resend, otherwise emails will not be delivered.

---

## 🔄 STEP 4️⃣ Email Utility Implementation (Resend)

📁 **File:** `src/utils/email.js`

This file handles **all email sending logic** using Resend.

```js
// ============================================
// 📋 EMAIL UTILITY (RESEND - PRODUCTION READY)
// ============================================
// Handles:
//  ✔ TAC (2FA) emails
//  ✔ Password reset emails
//  ✔ Welcome emails
//
// Email provider: Resend (API-based)
// ============================================

const { Resend } = require('resend');
require('dotenv').config();

const resend = new Resend(process.env.RESEND_API_KEY);
const FROM_EMAIL = process.env.EMAIL_FROM;
```

---

## 🎨 Keeping Original Email Design (Important)

All existing **HTML email styles** are preserved.

Only the **sending method** changes.

### Example: Password Reset Email

The original HTML layout and styling are reused **without modification**.
Only the Nodemailer `mailOptions` is replaced with `resend.emails.send()`.

```js
const sendResetEmail = async (email, resetCode) => {
  try {
    await resend.emails.send({
      from: FROM_EMAIL,
      to: email,
      subject: '🔒 GPS UTM - Password Reset Code',
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 2px solid #2563EB; border-radius: 10px;">
          <h2 style="color: #2563EB; text-align: center;">🎓 Gerakan Pengguna Siswa UTM</h2>
          <h3 style="text-align: center; color: #333;">Password Reset Request</h3>
          <hr style="border: 1px solid #2563EB;">
          <p style="font-size: 16px;">Your password reset code is:</p>
          <div style="background: #eef2ff; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;">
            <h1 style="color: #2563EB; letter-spacing: 10px; font-size: 36px; margin: 0;">
              ${resetCode}
            </h1>
          </div>
          <p style="color: #d97706; font-weight: bold;">⏰ This code expires in 15 minutes.</p>
          <p style="color: #666;">If you did not request this, you can safely ignore this email.</p>
          <hr style="border: 1px solid #e5e7eb; margin-top: 30px;">
          <p style="text-align: center; color: #999; font-size: 12px;">
            © 2025 Gerakan Pengguna Siswa UTM<br>
            Empowering students to become smart, ethical, and responsible consumers
          </p>
        </div>
      `
    });

    console.log(`✅ Password reset email sent to ${email}`);
  } catch (error) {
    console.error('❌ Failed to send password reset email:', error);
    throw error;
  }
};
```

---

