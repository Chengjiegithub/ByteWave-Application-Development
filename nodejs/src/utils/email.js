// ============================================
// 📋 EMAIL UTILITY WITH TEST MODE
// ============================================
// Supports:
//  ✔ Real email sending via Nodemailer
//  ✔ TAC_TEST_MODE = true → no email, return TAC in API response
// ============================================

const nodemailer = require('nodemailer');
require('dotenv').config();

const isTestMode = process.env.TAC_TEST_MODE === 'true';

// Create transporter (only if NOT in test mode)
let transporter = null;
if (!isTestMode) {
  transporter = nodemailer.createTransport({
    host: process.env.EMAIL_HOST,
    port: process.env.EMAIL_PORT,
    secure: false,
    auth: {
      user: process.env.EMAIL_USER,
      pass: process.env.EMAIL_PASS
    }
  });
}

// ============================================
// 📌 SEND TAC EMAIL (Supports Test Mode)
// ============================================
const sendTACEmail = async (email, tacCode) => {
  // TEST MODE: do not send email
  if (isTestMode) {
    console.log(`🔧 [TEST MODE] TAC for ${email}: ${tacCode}`);
    return {
      test: true,
      tac: tacCode
    };
  }

  // PRODUCTION MODE: send email normally
  try {
    const mailOptions = {
      from: process.env.EMAIL_USER,
      to: email,
      subject: '🔐 GPSphere - Your Two-Factor Authentication Code',
      html: `
        <h2>Two-Factor Authentication</h2>
        <p>Your TAC (Time-based Authentication Code) is:</p>
        <h1 style="color: #007bff; letter-spacing: 5px;">${tacCode}</h1>
        <p>This code expires in <strong>2 minutes</strong>.</p>
        <p>If you didn't request this, please ignore this email.</p>
      `
    };

    await transporter.sendMail(mailOptions);
    console.log(`✅ TAC email sent to ${email}`);

    return { test: false };
  } catch (error) {
    console.error('❌ Email sending failed:', error.message);
    throw error;
  }
};

// ============================================
// 📌 SEND WELCOME EMAIL (Also respects test mode)
// ============================================
const sendWelcomeEmail = async (email, name) => {
  if (isTestMode) {
    console.log(`🔧 [TEST MODE] Welcome email skipped for ${email}`);
    return { test: true };
  }

  try {
    const mailOptions = {
      from: process.env.EMAIL_USER,
      to: email,
      subject: '🎉 Welcome to GPSphere!',
      html: `
        <h2>Welcome, ${name}!</h2>
        <p>Your account has been created successfully.</p>
        <p>Your account is currently <strong>pending approval</strong> from the admin.</p>
        <p>You will receive an email once your account is approved.</p>
        <p>Best regards,<br>GPSphere Team</p>
      `
    };

    await transporter.sendMail(mailOptions);
    console.log(`✅ Welcome email sent to ${email}`);

    return { test: false };
  } catch (error) {
    console.error('❌ Email sending failed:', error.message);
    throw error;
  }
};

module.exports = {
  sendTACEmail,
  sendWelcomeEmail
};
