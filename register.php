<?php
include "db.php";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $birthday = $_POST["birthday"]; // Capture birthday from form

    if ($name == "" || $email == "" || $password == "" || $birthday == "") {
        $error = "All fields are required!";
    } else {
        // check existing email
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Updated INSERT to include birthday
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, birthday, points, membership_level) VALUES (?, ?, ?, ?, 0, 'Cloud Starter')");
            $stmt->bind_param("ssss", $name, $email, $hashed, $birthday);
            
            if($stmt->execute()){
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register | SugarCloudCafe</title>

<style>
/* ✅ YOUR DESIGN KEPT */
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial;}
body{
    background-color:#1b0f0a;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.register-card{
    background:#D7CCC8;
    padding:40px;
    border-radius:12px;
    width:350px;
    box-shadow:0 4px 12px rgba(0,0,0,0.5);
    color:#3E2723;
}
.register-card h2{text-align:center;margin-bottom:20px;}
label {
    font-size: 12px;
    font-weight: bold;
    display: block;
    margin-top: 10px;
}
input{
    width:100%;
    padding:10px;
    margin:5px 0 10px 0;
    border-radius:6px;
    border:1px solid #3E2723;
}
button{
    width:100%;
    padding:10px;
    margin-top:10px;
    border:none;
    border-radius:6px;
    background:#3E2723;
    color:#fff;
    cursor:pointer;
    font-weight:bold;
}
button:hover{background:#5D4037;}
.error{
    color:red;
    text-align:center;
    margin-top:10px;
}
.login-link{
    text-align:center;
    margin-top:10px;
}
.login-link a{
    color:#3E2723;
    font-weight:bold;
    text-decoration:none;
}
</style>
</head>
<body>

<div class="register-card">
    <h2>Register</h2>

    <form method="post">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        
        <label for="birthday">Date of Birth</label>
        <input type="date" name="birthday" id="birthday" required>
        
        <button type="submit">Register</button>
    </form>

    <?php if($error!=""): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <div class="login-link">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>