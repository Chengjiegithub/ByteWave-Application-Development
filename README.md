# ByteWave Website Development
Here is the Github for the development of GPSphere

# Important 
If you are using my code. Please take note that my MySQL port was forced to change to 3307. Please change the port in the code if you are using my code.


# Steps on how to start
1. Open XAMPP and start APACHE and MySQL
2. Browse http://localhost/GPSphere/ to get to the Index of/GPSphere
3. Also browse http://localhost/phpmyadmin for the database.
4. Run create_database.php to create the user database and an admin account in the users table.
5. Check the databse at the phpmyadmin. You should have a gpsphere_db
6. Follow the steps at for the app password for TAC 2FA email
7. After finish follow, you can try the 2FA TAC with the Admin account.




# How to Generate a Gmail App Password for PHPMailer
App Password is needed by the phpmailer to get to work. Please follow it

Step 1 — Turn On 2-Step Verification
Go to 👉 https://myaccount.google.com/
On the left menu, click “Security.”
Scroll to “Signing in to Google.”

Click “2-Step Verification.”
Follow the prompts to turn it on (you’ll verify using your phone).
🔸 This step is required before you can create an App Password.

✅ Step 2 — Open “App Passwords”
After enabling 2-Step Verification, go back to:
👉 https://myaccount.google.com/apppasswords
Sign in again if prompted.
Under “Select app,” choose Mail.
Under “Select device,” choose Other (Custom name) and type GPSphere.
Click Generate.

✅ Step 3 — Copy the 16-Character App Password
A yellow box will appear with something like:
abcd efgh ijkl mnop
That’s your App Password (ignore the spaces).

✅ Step 4 — Use It in PHPMailer
In your login.php, replace:
$mail->Username = 'YOUR_GMAIL@gmail.com';
$mail->Password = 'YOUR_APP_PASSWORD';
with your actual Gmail and the App Password (without spaces).

Example:
$mail->Username = 'chengjieutm@gmail.com';
$mail->Password = 'abcdijklmnopqrst';

✅ Step 5 — Save and Test
Save login.php
Run XAMPP → start Apache + MySQL
Go to http://localhost/GPSphere/login.php
Login and check your Gmail inbox — you should receive the TAC email 🎉




