<?php
session_start();
include "db.php"; 

// 1. SEMAK STATUS LOGIN
$isLoggedIn = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

$success = false;
$error = "";

// 2. LOGIK SIMPAN DATA (Hanya jika sudah login)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $isLoggedIn) {
    $name    = trim($_POST["full_name"]);
    $email   = trim($_POST["email"]);
    $phone   = trim($_POST["phone"]);
    $date    = trim($_POST["date"]);
    $time    = trim($_POST["time"]);
    $guests  = (int)$_POST["guests"];
    $remarks = trim($_POST["remarks"]);

    if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($time) || $guests <= 0) {
        $error = "Please fill in all required fields!";
    } else {
        $stmt = $conn->prepare("INSERT INTO reservations (full_name, email, phone, reserve_date, reserve_time, guests, remarks) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssis", $name, $email, $phone, $date, $time, $guests, $remarks);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Failed to save reservation. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation | SugarCloudCafe</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --accent: #e2c1a9;
            --bg-dark: #1b0f0a;
            --card-bg: #2b1a13;
            --input-bg: rgba(255, 255, 255, 0.07);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body {
            background: var(--bg-dark);
            background-image: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('images/cafe-bg.jpg'); 
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
            color: white;
        }

        .container {
            background: rgba(43, 26, 19, 0.95);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.9);
            border: 1px solid rgba(226, 193, 169, 0.1);
            position: relative;
        }

        .container h2 {
            font-family: "Great Vibes", cursive;
            font-size: 45px;
            text-align: center;
            margin-bottom: 5px;
            color: var(--accent);
        }

        .subtitle { text-align: center; color: #aaa; font-size: 0.9rem; margin-bottom: 30px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 0.8rem; color: var(--accent); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px; }

        input, textarea, select {
            width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);
            outline: none; background: var(--input-bg); color: white; transition: 0.3s;
        }

        select { background: var(--card-bg); cursor: pointer; appearance: none; }

        .row { display: flex; gap: 15px; }
        .row .form-group { flex: 1; }

        button {
            width: 100%; padding: 15px; margin-top: 10px; border: none; border-radius: 10px;
            font-weight: 600; font-size: 1rem; background: var(--accent); color: var(--bg-dark);
            cursor: pointer; transition: 0.3s;
        }

        button:hover { background: white; transform: translateY(-2px); }

        /* --- POPUP STYLES --- */
        .modal-overlay {
            display: <?= $isLoggedIn ? 'none' : 'flex' ?>; 
            position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.9); backdrop-filter: blur(10px);
            justify-content: center; align-items: center;
        }

        .modal-card {
            background: #2b1a13; padding: 45px 40px; border-radius: 25px; text-align: center;
            border: 1px solid rgba(226, 193, 169, 0.2); width: 90%; max-width: 420px;
        }

        .notice-title {
            font-weight: 700; text-transform: uppercase; letter-spacing: 3px;
            color: var(--accent); font-size: 28px; margin-bottom: 15px; display: block;
        }

        .popup-btn {
            display: inline-block; padding: 12px 25px; border-radius: 12px; font-weight: 600;
            text-decoration: none; transition: 0.3s; margin: 5px;
        }
        .btn-primary { background: var(--accent); color: #1b0f0a; }
        .btn-secondary { background: rgba(255,255,255,0.1); color: #fff; }

        .back-home { display: block; text-align: center; margin-top: 25px; text-decoration: none; color: #888; font-size: 0.85rem; }
        .msg { padding: 15px; border-radius: 10px; text-align: center; margin-bottom: 20px; }
        .error { background: rgba(255, 71, 71, 0.15); color: #ff4747; border: 1px solid #ff4747; }
        .success { background: rgba(46, 213, 115, 0.15); color: #2ed573; border: 1px solid #2ed573; }

        @media (max-width: 480px) { .row { flex-direction: column; gap: 0; } }
    </style>
</head>
<body>

<?php if(!$isLoggedIn): ?>
<div class="modal-overlay">
    <div class="modal-card">
        <span class="notice-title">NOTICE</span>
        <div style="width: 40px; height: 2px; background: var(--accent); margin: 0 auto 20px;"></div>
        <p style="font-weight: 300; font-size: 15px; margin-bottom: 30px; color: #eee; line-height: 1.6;">
            To ensure a seamless experience, please <strong>Login</strong> or <strong>Register</strong> before making a table reservation.
        </p>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="login.php" class="popup-btn btn-primary">Login Now</a>
            <a href="index.php" class="popup-btn btn-secondary">Back to Home</a>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <h2>Table Reservation</h2>
    <p class="subtitle">Experience the magic of SugarCloudCafe</p>

    <?php if($error != ""): ?>
        <div class="msg error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="msg success">✨ Reservation successful! We will contact you soon.</div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="Enter your name" required <?= !$isLoggedIn ? 'disabled' : '' ?>>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="name@email.com" required <?= !$isLoggedIn ? 'disabled' : '' ?>>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" placeholder="01X-XXXXXXX" required <?= !$isLoggedIn ? 'disabled' : '' ?>>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" min="<?= date('Y-m-d'); ?>" required <?= !$isLoggedIn ? 'disabled' : '' ?>>
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="time" name="time" required <?= !$isLoggedIn ? 'disabled' : '' ?>>
            </div>
        </div>

        <div class="form-group">
            <label>Number of Guests</label>
            <select name="guests" required <?= !$isLoggedIn ? 'disabled' : '' ?>>
                <option value="" disabled selected>Select number of people</option>
                <?php for($i=1;$i<=10;$i++): ?>
                    <option value="<?= $i ?>"><?= $i ?> Person<?= $i>1?'s':'' ?></option>
                <?php endfor; ?>
                <option value="11">More than 10 (Specify in remarks)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Special Requests (Optional)</label>
            <textarea name="remarks" rows="3" placeholder="E.g., Birthday celebration, food allergies, etc." <?= !$isLoggedIn ? 'disabled' : '' ?>></textarea>
        </div>

        <button type="submit" <?= !$isLoggedIn ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
            Confirm Reservation
        </button>
    </form>

    <a href="index.php" class="back-home">← Back to Home Page</a>
</div>

</body>
</html>