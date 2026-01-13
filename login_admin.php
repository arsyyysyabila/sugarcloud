<?php
session_start();
require_once('db.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Secure query to find admin
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Checking plain text for now as per your setup, 
        // but password_verify() is recommended for hashed passwords.
        if ($pass === $row['password']) {
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_id'] = $row['id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Admin not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | SugarCloudCafe</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            /* 1. BACKGROUND */
            background: #1b0f0a;
            background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/bg3.jpg');
            background-size: cover;
            background-position: center;

            /* 2. FONT FAMILY */
            font-family: 'Poppins', sans-serif;
            color: white;

            /* 3. LAYOUT */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            /* 1. BACKGROUND */
            background: rgba(43, 26, 19, 0.95);
            border: 1px solid #d4af7f;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);

            /* 2. FONT FAMILY */
            text-align: center;

            /* 3. LAYOUT */
            width: 350px;
            padding: 40px;
        }

        input {
            /* 1. BACKGROUND */
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 127, 0.3);
            border-radius: 8px;

            /* 2. FONT FAMILY */
            font-family: 'Poppins', sans-serif;
            color: white;

            /* 3. LAYOUT */
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        .login-btn {
            /* 1. BACKGROUND */
            background: #d4af7f;
            border: none;
            border-radius: 8px;

            /* 2. FONT FAMILY */
            color: #1b0f0a;
            font-weight: 600;

            /* 3. LAYOUT */
            width: 100%;
            padding: 12px;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2 style="color: #d4af7f; margin-bottom: 20px;">Admin Access</h2>
    <?php if($error): ?>
        <p style="color: #ff8e8e; font-size: 13px;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="login-btn">Login to Dashboard</button>
    </form>
</div>

</body>
</html>