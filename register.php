<?php include('config.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>GPSphere | Register</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h2>Student Registration</h2>
<form method="POST" action="">
    <input type="text" name="name" placeholder="Full Name" required><br>
    <input type="email" name="email" placeholder="Student Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="register">Register</button>
</form>

<?php
if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name,email,password) VALUES ('$name','$email','$password')";
    if($conn->query($sql)){
        echo "<p>Registration successful! Please login.</p>";
    } else {
        echo "<p>Error: ".$conn->error."</p>";
    }
}
?>
</body>
</html>