// ============================================
// 📋 EMAIL UTILITY (PRODUCTION)
// ============================================
// Supports:
//  ✔ Real email sending via Nodemailer
// ============================================

const nodemailer = require('nodemailer');
require('dotenv').config();

let transporter = null;
  transporter = nodemailer.createTransport({
    host: process.env.EMAIL_HOST,
    port: process.env.EMAIL_PORT,
    secure: false,
    auth: {
      user: process.env.EMAIL_USER,
      pass: process.env.EMAIL_PASS
    }
  });

// ============================================
// 📌 SEND TAC EMAIL
// ============================================
const sendTACEmail = async (email, tacCode) => {
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
// 📌 SEND PASSWORD RESET EMAIL 
// ============================================
const sendResetEmail = async (email, resetCode) => {
  try {
    const mailOptions = {
      from: `"GPS UTM" <${process.env.EMAIL_USER}>`,
      to: email,
      subject: '🔒 GPS UTM - Password Reset Code',
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 2px solid #2563EB; border-radius: 10px;">
          <h2 style="color: #2563EB; text-align: center;">🎓 Gerakan Pengguna Siswa UTM</h2>
          <h3 style="text-align: center; color: #333;">Password Reset Request</h3>
          <hr style="border: 1px solid #2563EB;">
          <p style="font-size: 16px;">Your password reset code is:</p>
          <div style="background: #eef2ff; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;">
            <h1 style="color: #2563EB; letter-spacing: 10px; font-size: 36px; margin: 0;">${resetCode}</h1>
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
    };

    await transporter.sendMail(mailOptions);
    console.log(`✅ Password reset email sent to ${email}`);
    return { test: false };
  } catch (error) {
    console.error('❌ Password reset email failed:', error.message);
    throw error;
  }
};

// ============================================
// 📌 SEND WELCOME EMAIL 
// ============================================
const sendWelcomeEmail = async (email, name) => {
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
  sendResetEmail,
  sendWelcomeEmail
};
