# 📧 EMAIL CONFIGURATION GUIDE

## Getting Gmail App Password

Your `.env` file needs Gmail credentials. Here's how to get them:

### Step 1: Enable 2-Factor Authentication

1. Go to https://myaccount.google.com/
2. Click "Security" (left menu)
3. Find "2-Step Verification"
4. Click and follow the prompts
5. Verify your phone

### Step 2: Create App Password

1. Go back to https://myaccount.google.com/security
2. Find "App passwords" (only visible if 2FA is on)
3. Select: 
   - App: Mail
   - Device: Windows/Mac/Other
4. Click "Generate"
5. Gmail shows a 16-character password

**Example:**
```
abcd efgh ijkl mnop
```

### Step 3: Update .env File

Copy the password WITHOUT spaces:

```env
EMAIL_USER=your_email@gmail.com
EMAIL_PASS=abcdefghijklmnop
```

---

## Testing Email

### Test 1: Via Server Console

When you login, check the server console (where `npm run dev` runs).

You should see:
```
✅ TAC email sent to your_email@gmail.com
```

### Test 2: Check Received Email

Your email should have:
- Subject: "🔐 GPSphere - Your Two-Factor Authentication Code"
- Body: Contains 6-digit TAC code and expiry time

### Test 3: Via Code

```javascript
const { sendTACEmail } = require('./src/utils/email');

// Test sending
await sendTACEmail('test@example.com', '123456');
// Console shows: ✅ TAC email sent to test@example.com
```

---

## Common Email Issues

### Issue: "Invalid login credentials"
**Solution:** 
- Make sure 2FA is enabled
- Use App Password (not regular password)
- Check you copied the password correctly (no spaces)

### Issue: "Less secure apps blocked"
**Solution:**
- Use App Password method above (recommended)
- OR enable "Less secure app access" at https://myaccount.google.com/lesssecureapps

### Issue: "Invalid sender address"
**Solution:**
- Make sure EMAIL_USER matches a real Gmail account
- Email sending FROM account must exist

### Issue: "Email not sending but no error"
**Solution:**
- Check .env file is saved correctly
- Check `NODE_ENV=development` in .env
- Restart server: `npm run dev`
- Check console logs for errors

---

## Alternative Email Providers

### Using Outlook/Hotmail

```env
EMAIL_HOST=smtp-mail.outlook.com
EMAIL_PORT=587
EMAIL_USER=your_email@outlook.com
EMAIL_PASS=your_password
```

### Using SendGrid

```env
EMAIL_HOST=smtp.sendgrid.net
EMAIL_PORT=587
EMAIL_USER=apikey
EMAIL_PASS=SG.your_sendgrid_api_key
```

---

## Email Templates

You can customize the email messages in `src/utils/email.js`:

### TAC Email Template

```javascript
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
```

### Welcome Email Template

```javascript
const mailOptions = {
  from: process.env.EMAIL_USER,
  to: email,
  subject: '🎉 Welcome to GPSphere!',
  html: `
    <h2>Welcome, ${name}!</h2>
    <p>Your account has been created successfully.</p>
    <p>Your account is currently <strong>pending approval</strong> from the admin.</p>
  `
};
```

You can edit these to match your branding!

---

## Monitoring Email Delivery

### Enable Email Logging

Edit `src/utils/email.js`:

```javascript
// Add before transporter.sendMail()
console.log('📤 Sending email to:', email);
console.log('📝 Subject:', mailOptions.subject);

await transporter.sendMail(mailOptions);

console.log('✅ Email sent successfully');
```

Then restart server and check console output.

---

## Testing with Fake Emails

For development, you can use **Mailtrap** (fake Gmail):

1. Go to https://mailtrap.io/
2. Create free account
3. Get SMTP settings
4. Update .env:

```env
EMAIL_HOST=smtp.mailtrap.io
EMAIL_PORT=2525
EMAIL_USER=your_mailtrap_user
EMAIL_PASS=your_mailtrap_pass
```

5. Emails go to Mailtrap inbox instead of real email

---

## Production Email Best Practices

1. **Use App Password** (not regular password)
2. **Never commit .env to GitHub** - use .gitignore
3. **Change JWT_SECRET in production**
4. **Monitor email delivery** - check logs
5. **Use professional sender name** - set it in code
6. **Add unsubscribe link** for compliance
7. **Test email content** on all clients

---

Happy emailing! 📧
