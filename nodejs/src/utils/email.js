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
      from: `"GPS UTM" <${process.env.EMAIL_USER}>`,
      to: email,
      subject: '🔐 GPS UTM - Your Authentication Code',
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 2px solid #10b981; border-radius: 10px;">
          <h2 style="color: #059669; text-align: center;">🎓 Gerakan Pengguna Siswa UTM</h2>
          <h3 style="text-align: center; color: #333;">Student Consumer Movement</h3>
          <hr style="border: 1px solid #10b981;">
          <h2 style="color: #333;">Two-Factor Authentication</h2>
          <p style="font-size: 16px;">Your TAC (Time-based Authentication Code) is:</p>
          <div style="background: #f0fdf4; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;">
            <h1 style="color: #059669; letter-spacing: 10px; font-size: 48px; margin: 0;">${tacCode}</h1>
          </div>
          <p style="color: #d97706; font-weight: bold;">⏰ This code expires in 15 minutes.</p>
          <p style="color: #666;">If you didn't request this code, please ignore this email.</p>
          <hr style="border: 1px solid #e5e7eb; margin-top: 30px;">
          <p style="text-align: center; color: #999; font-size: 12px;">
            © 2025 Gerakan Pengguna Siswa UTM<br>
            Empowering students to become smart, ethical, and responsible consumers
          </p>
        </div>
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
      from: `"GPS UTM" <${process.env.EMAIL_USER}>`,
      to: email,
      subject: '🎉 Welcome to GPS UTM - Gerakan Pengguna Siswa!',
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 2px solid #10b981; border-radius: 10px;">
          <h2 style="color: #059669; text-align: center;">🎓 Gerakan Pengguna Siswa UTM</h2>
          <h3 style="text-align: center; color: #333;">Student Consumer Movement</h3>
          <hr style="border: 1px solid #10b981;">
          <h2 style="color: #333;">Welcome, ${name}!</h2>
          <p style="font-size: 16px;">Thank you for joining GPS UTM! Your account has been created successfully.</p>
          <div style="background: #fef3c7; padding: 15px; border-left: 4px solid #d97706; margin: 20px 0;">
            <p style="margin: 0; color: #92400e;">⏳ <strong>Account Status:</strong> Pending Approval</p>
          </div>
          <p>Your account is currently under review by our administrators. You will receive an email notification once your account is approved.</p>
          <h3 style="color: #059669;">What's Next?</h3>
          <ul style="line-height: 1.8;">
            <li>Wait for admin approval (usually 1-2 business days)</li>
            <li>Once approved, you can join our consumer education programs</li>
            <li>Learn about consumer rights and responsibilities</li>
            <li>Participate in workshops and activities</li>
          </ul>
          <hr style="border: 1px solid #e5e7eb; margin-top: 30px;">
          <p style="text-align: center; color: #666;">
            Best regards,<br>
            <strong>GPS UTM Team</strong>
          </p>
          <p style="text-align: center; color: #999; font-size: 12px;">
            © 2025 Gerakan Pengguna Siswa UTM<br>
            Empowering students to become smart, ethical, and responsible consumers
          </p>
        </div>
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
