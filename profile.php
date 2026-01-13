<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// FETCH USER DATA - Menggunakan kolum 'phonenum' seperti dalam DB anda
$stmt = $conn->prepare("SELECT name, points, membership_level, birthday, phonenum FROM customers WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | SugarCloudCafe</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --bg-color: #1b0f0a; /* Dark Espresso dari skrin anda */
            --card-bg: #2b1a13;   /* Muted Dark Brown */
            --accent: #e2c1a9;    /* Muted Rose/Sand dari butang anda */
            --text-main: #ffffff;
            --text-muted: rgba(226, 193, 169, 0.7);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            padding-top: 40px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            padding: 20px;
        }

        /* Back to Home Button */
        .back-link {
            text-decoration: none;
            color: var(--accent);
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .back-link:hover {
            opacity: 0.7;
            transform: translateX(-5px);
        }

        /* Profile Header */
        .profile-card {
            background-color: var(--card-bg);
            border-radius: 20px;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }

        .member-info h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin: 5px 0;
            color: var(--text-main);
        }

        .membership-tag {
            background-color: var(--accent);
            color: var(--bg-color);
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .points-circle {
            width: 100px;
            height: 100px;
            border: 1px solid var(--accent);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .points-circle span { font-size: 1.8rem; font-weight: 600; color: var(--text-main); }
        .points-circle small { font-size: 0.6rem; color: var(--accent); text-transform: uppercase; }

        /* Settings Panel */
        .settings-panel {
            background-color: var(--card-bg);
            border-radius: 20px;
            padding: 40px;
        }

        .settings-panel h3 {
            border-left: 3px solid var(--accent);
            padding-left: 15px;
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: var(--accent);
        }

        .form-group { margin-bottom: 25px; }
        label {
            display: block;
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        input {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(226, 193, 169, 0.2);
            color: #fff;
            padding: 10px 0;
            font-size: 1rem;
            transition: 0.3s;
        }

        input:focus { outline: none; border-bottom: 1px solid var(--accent); }
        input[readonly] { cursor: default; }

        .btn-edit {
            width: 100%;
            background: transparent;
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 15px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            margin-top: 10px;
            transition: 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-edit:hover { background: var(--accent); color: var(--bg-color); }

        .btn-save {
            width: 100%;
            background: var(--accent);
            color: var(--bg-color);
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="back-link"><i class="fas fa-chevron-left"></i> Back to Home</a>

    <div class="profile-card">
        <div class="member-info">
            <span class="membership-tag"><?= htmlspecialchars($user['membership_level'] ?? 'Cloud Starter') ?></span>
            <h1><?= htmlspecialchars($user['name']) ?></h1>
            <p style="font-size: 0.8rem; color: var(--text-muted);"><i class="far fa-calendar-alt"></i> SugarCloud Member</p>
        </div>
        <div class="points-circle">
            <span><?= number_format($user['points'] ?? 0) ?></span>
            <small>Points</small>
        </div>
    </div>

    <div class="settings-panel">
        <h3>Account Settings</h3>
        <form action="update_profile.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" id="nameInp" readonly>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phonenum" value="<?= htmlspecialchars($user['phonenum']) ?>" id="phoneInp" readonly>
            </div>
            <div class="form-group">
                <label>Birthday</label>
                <input type="date" name="birthday" value="<?= $user['birthday'] ?>" id="bdayInp" readonly>
            </div>

            <button type="button" id="editBtn" class="btn-edit"><i class="fas fa-pen-nib"></i> Edit Profile</button>
            <button type="submit" id="saveBtn" class="btn-save" style="display:none;">Save Changes</button>
        </form>
    </div>
</div>

<script>
    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');
    const inputs = [document.getElementById('nameInp'), document.getElementById('phoneInp'), document.getElementById('bdayInp')];

    editBtn.onclick = () => {
        inputs.forEach(i => i.removeAttribute('readonly'));
        editBtn.style.display = 'none';
        saveBtn.style.display = 'block';
        inputs[0].focus();
    }

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Refined!',
            text: 'Profile updated successfully.',
            background: '#2b1a13',
            color: '#fff',
            confirmButtonColor: '#e2c1a9'
        });
    }
</script>
</body>
</html>