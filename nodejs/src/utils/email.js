// ============================================
// 📋 STEP 3: EMAIL UTILITY (replaces PHPMailer)
// ============================================
// This file handles all email sending using Nodemailer

const nodemailer = require('nodemailer');
require('dotenv').config();

// Create transporter (like PHPMailer configuration)
const transporter = nodemailer.createTransport({
  host: process.env.EMAIL_HOST,
  port: process.env.EMAIL_PORT,
  secure: false, // true for 465, false for other ports
  auth: {
    user: process.env.EMAIL_USER,
    pass: process.env.EMAIL_PASS
  }
});

// Function to send 2FA TAC code
const sendTACEmail = async (email, tacCode) => {
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
    return true;
  } catch (error) {
    console.error('❌ Email sending failed:', error.message);
    throw error;
  }
};

// Function to send welcome email after registration
const sendWelcomeEmail = async (email, name) => {
  try {
    const mailOptions = {
      from: process.env.EMAIL_USER,
      to: email,
      subject: '🎉 Welcome to GPSphere!',
      html: `
        <h2>Welcome, ${name}!</h2>
        <p>Your account has been created successfully.</p>
        <p>Your account is currently <strong>pending approval</strong> from the admin.</p>
        <p>You'll receive an email once your account is approved.</p>
        <p>Best regards,<br>GPSphere Team</p>
      `
    };

    await transporter.sendMail(mailOptions);
    console.log(`✅ Welcome email sent to ${email}`);
    return true;
  } catch (error) {
    console.error('❌ Email sending failed:', error.message);
    throw error;
  }
};

module.exports = {
  sendTACEmail,
  sendWelcomeEmail
};
