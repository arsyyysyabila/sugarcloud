<?php
session_start();
require_once('db.php');

$error = "";
$success = "";

// --- 1. HANDLE REGISTRATION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = trim($_POST['reg_name']);
    $email = trim($_POST['reg_email']);
    $pass = trim($_POST['reg_password']);
    $repass = trim($_POST['reg_repassword']);

    // Check if passwords match
    if ($pass !== $repass) {
        $error = "Passwords do not match!";
    } else {
        $check = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // Simpan password secara plain text mengikut kod asal anda (Disarankan guna password_hash untuk projek sebenar)
            $stmt = $conn->prepare("INSERT INTO customers (name, email, password, points, membership_level) VALUES (?, ?, ?, 0, 'Cloud Starter')");
            $stmt->bind_param("sss", $name, $email, $pass);
            
            if ($stmt->execute()) {
                $success = "Welcome to the Club! Please login to start ordering.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

// --- 2. HANDLE LOGIN ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $role = $_POST["role"] ?? "customer";

    if ($role === "admin") {
        $stmt = $conn->prepare("SELECT id, password, full_name FROM admins WHERE username = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($password === $row['password']) {
                $_SESSION['is_admin'] = true;
                $_SESSION['user_name'] = $row['full_name'];
                $_SESSION['login_success'] = true;
                header("Location: dashboard.php");
                exit();
            }
        }
    } else {
        $stmt = $conn->prepare("SELECT id, password, name, points, membership_level FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if ($password === $row['password']) {
                $_SESSION['is_logged_in'] = true; 
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                
                $pts = $row['points'];
                $tier = "Cloud Starter";
                if ($pts >= 500) $tier = "Cloud Elite";
                elseif ($pts >= 100) $tier = "Sugar Lover";
                
                $_SESSION['points'] = $pts;
                $_SESSION['member_level'] = $tier;
                $_SESSION['login_success'] = true;
                
                header("Location: menu.php");
                exit();
            }
        }
    }
    $error = "Invalid $role credentials!";
}

// --- 3. HANDLE FORGOT PASSWORD (LOGIC SIMULATION) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_request'])) {
    $email = trim($_POST['reset_email']);
    // Anda boleh tambah logik semakan email di sini
    $success = "Reset link has been sent to your email!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Login | SugarCloudCafe</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #d4af7f;
            --bg-dark: #1b0f0a;
        }

        body {
            background: url('images/dessert11.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            color: white;
            margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; overflow: hidden;
        }

        .login-box, .modal-content {
            background: rgba(43, 26, 19, 0.96);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.8);
            border: 1px solid rgba(212, 175, 127, 0.3);
            text-align: center;
            padding: 40px; width: 400px; box-sizing: border-box;
        }

        h2 { font-family: 'Great Vibes', cursive; color: var(--accent); font-size: 45px; margin: 0 0 10px 0; }

        .role-switch { background: rgba(0, 0, 0, 0.4); border-radius: 12px; font-size: 13px; display: flex; margin-bottom: 25px; padding: 5px; }
        .role-switch label { flex: 1; padding: 10px; cursor: pointer; border-radius: 10px; }
        .role-switch input { display: none; }
        .role-switch input:checked + span { background: var(--accent); color: #2b1f17; display: block; width: 100%; border-radius: 8px; font-weight: 600; }

        input { background: rgba(255,255,255,0.05); border: 1px solid rgba(212, 175, 127, 0.2); border-radius: 10px; color: white; font-size: 15px; width: 100%; padding: 12px; margin-bottom: 15px; box-sizing: border-box; }

        .btn-main { background: linear-gradient(to right, #d4af7f, #e2c1a9); border: none; border-radius: 12px; color: #2b1f17; font-weight: 600; width: 100%; padding: 14px; cursor: pointer; transition: 0.3s; }
        .btn-main:hover { transform: scale(1.02); }

        .links { font-size: 13px; margin-top: 20px; display: flex; justify-content: space-between; }
        .links a { color: var(--accent); text-decoration: none; cursor: pointer; }

        .modal { display: none; position: fixed; z-index: 100; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); justify-content: center; align-items: center; }

        .cancel-btn { color:#d4af7f; font-size:12px; display:block; margin-top:15px; cursor:pointer; text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>SugarCloud</h2>
    <p style="color: #d4af7f; font-size: 14px; margin-bottom: 20px;">Member Exclusive Access</p>
    
    <form method="POST">
        <div class="role-switch">
            <label><input type="radio" name="role" value="customer" checked><span>Customer</span></label>
            <label><input type="radio" name="role" value="admin"><span>Admin</span></label>
        </div>

        <input type="text" name="email" placeholder="Email / Username" required>
        <input type="password" name="password" placeholder="Password" required>
        
        <button type="submit" name="login" class="btn-main">Login to Order</button>
    </form>

    <div class="links">
        <a onclick="document.getElementById('forgotModal').style.display='flex'">Forgot Password?</a>
        <a onclick="document.getElementById('regModal').style.display='flex'">Join as Member</a>
    </div>

    <?php if($error): ?><p style="color:#ff8e8e; font-size:12px; margin-top:15px;"><?= $error ?></p><?php endif; ?>
    <?php if($success): ?><p style="color:#8eff8e; font-size:12px; margin-top:15px;"><?= $success ?></p><?php endif; ?>
</div>

<div id="regModal" class="modal">
    <div class="modal-content">
        <h2>Register Member</h2>
        <form method="POST">
            <input type="text" name="reg_name" placeholder="Username / Full Name" required>
            <input type="email" name="reg_email" placeholder="Email Address" required>
            <input type="password" name="reg_password" id="reg_pass" placeholder="Create Password" required>
            <input type="password" name="reg_repassword" id="reg_repass" placeholder="Retype Password" required>
            <button type="submit" name="register" class="btn-main">Create Member Account</button>
        </form>
        <a onclick="document.getElementById('regModal').style.display='none'" class="cancel-btn">Cancel</a>
    </div>
</div>

<div id="forgotModal" class="modal">
    <div class="modal-content">
        <h2>Reset Password</h2>
        <p style="font-size: 13px; color: #ccc; margin-bottom: 20px;">Enter your email to receive a password reset link.</p>
        <form method="POST">
            <input type="email" name="reset_email" placeholder="Registered Email Address" required>
            <button type="submit" name="reset_request" class="btn-main">Send Reset Link</button>
        </form>
        <a onclick="document.getElementById('forgotModal').style.display='none'" class="cancel-btn">Back to Login</a>
    </div>
</div>

<script>
    // Tutup modal jika klik di luar kotak putih
    window.onclick = function(e) { 
        if (e.target.className == 'modal') {
            document.getElementById('regModal').style.display = "none"; 
            document.getElementById('forgotModal').style.display = "none"; 
        }
    }
</script>

</body>
</html>