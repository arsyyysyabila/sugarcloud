<?php
session_start();
include('db.php');

// 1. AUTHENTICATION CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. FETCH USER POINTS & LEVEL
$stmt = $conn->prepare("SELECT points, membership_level FROM customers WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$current_points = $user['points'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Rewards | SugarCloudCafe</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { 
            --accent: #e2c1a9; 
            --dark-bg: #1b0f0a; 
            --card-bg: #2b1a13; 
            --glass: rgba(255, 255, 255, 0.03); 
        }

        body { 
            background: var(--dark-bg); 
            font-family: 'Poppins', sans-serif; 
            color: white; 
            margin: 0; 
            min-height: 100vh; 
        }
        
        .container { max-width: 900px; margin: 40px auto; padding: 20px; }
        
        .home-link { 
            text-decoration: none; 
            color: var(--accent); 
            font-size: 13px; 
            display: inline-flex; 
            align-items: center; 
            gap: 10px; 
            margin-bottom: 30px; 
            transition: 0.3s; 
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 500;
        }
        .home-link:hover { opacity: 0.7; transform: translateX(-5px); }

        .points-dashboard {
            background: var(--card-bg);
            border-radius: 25px; 
            padding: 50px 40px; 
            text-align: center;
            border: 1px solid rgba(226, 193, 169, 0.1);
            margin-bottom: 40px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .points-value { 
            font-family: 'Playfair Display', serif;
            font-size: 70px; 
            color: white; 
            margin: 10px 0; 
        }
        
        .voucher-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
        
        .voucher-card {
            background: var(--card-bg);
            border-radius: 20px; 
            padding: 30px; 
            border: 1px solid rgba(226, 193, 169, 0.1);
            position: relative; 
            overflow: hidden; 
            transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .voucher-card:hover { transform: translateY(-10px); border-color: var(--accent); }
        
        .voucher-card::before, .voucher-card::after {
            content: ''; position: absolute; top: 50%; transform: translateY(-50%);
            width: 30px; height: 30px; background: var(--dark-bg); border-radius: 50%;
        }
        .voucher-card::before { left: -15px; }
        .voucher-card::after { right: -15px; }

        .redeem-btn {
            background: var(--accent); 
            color: var(--dark-bg); 
            border: none;
            padding: 14px; 
            border-radius: 12px; 
            font-weight: 600;
            cursor: pointer; 
            width: 100%; 
            margin-top: 20px; 
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .redeem-btn:hover:not(:disabled) { background: #d4b49c; transform: scale(1.02); }
        .redeem-btn:disabled { background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.2); cursor: not-allowed; }

        .cafe-popup { background: #1b0f0a !important; color: white !important; border: 1px solid var(--accent) !important; border-radius: 25px !important; }

        h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--accent);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        h3::after { content: ''; flex: 1; height: 1px; background: rgba(226, 193, 169, 0.2); }
    </style>
</head>
<body>

<div class="container animate__animated animate__fadeIn">
    <a href="index.php" class="home-link">
        <i class="fas fa-home"></i> Back to Home
    </a>

    <div class="points-dashboard">
        <p style="letter-spacing: 3px; font-size: 0.75rem; opacity: 0.6; text-transform: uppercase;">Cloud Reward Balance</p>
        <div class="points-value"><?= number_format($current_points) ?></div>
        <span style="border: 1px solid var(--accent); padding: 6px 20px; border-radius: 50px; font-size: 11px; color: var(--accent); text-transform: uppercase; font-weight: 600; letter-spacing: 1px;">
            Status: <?= htmlspecialchars($user['membership_level'] ?? 'Lover') ?>
        </span>
    </div>

    <h3>Available Privileges</h3>

    <div class="voucher-grid">
        <div class="voucher-card">
            <small style="color: var(--accent); letter-spacing: 1px;">COFFEE TREAT</small>
            <h2 style="font-family: 'Playfair Display', serif; margin: 10px 0; font-size: 1.8rem;">RM 5.00 OFF</h2>
            <p style="font-size: 12px; opacity: 0.5; font-weight: 300;">Valid for any handcrafted coffee beverage.</p>
            <div style="border-top: 1px dashed rgba(226, 193, 169, 0.2); margin: 20px 0; padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; color: var(--accent);"><i class="fas fa-coins"></i> 100 PTS</span>
            </div>
            <button class="redeem-btn" <?= ($current_points < 100) ? 'disabled' : '' ?> onclick="confirmRedemption('COFFEE TREAT (RM5 OFF)', 100)">
                <?= ($current_points < 100) ? 'Insufficient Points' : 'Redeem Voucher' ?>
            </button>
        </div>

        <div class="voucher-card">
            <small style="color: var(--accent); letter-spacing: 1px;">SWEET DELIGHT</small>
            <h2 style="font-family: 'Playfair Display', serif; margin: 10px 0; font-size: 1.8rem;">FREE COOKIE</h2>
            <p style="font-size: 12px; opacity: 0.5; font-weight: 300;">Redeem one signature SugarCloud cookie of your choice.</p>
            <div style="border-top: 1px dashed rgba(226, 193, 169, 0.2); margin: 20px 0; padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; color: var(--accent);"><i class="fas fa-coins"></i> 250 PTS</span>
            </div>
            <button class="redeem-btn" <?= ($current_points < 250) ? 'disabled' : '' ?> onclick="confirmRedemption('FREE COOKIE REWARD', 250)">
                <?= ($current_points < 250) ? 'Insufficient Points' : 'Redeem Voucher' ?>
            </button>
        </div>
    </div>
</div>

<script>
function confirmRedemption(rewardName, cost) {
    Swal.fire({
        title: `<span style="color: #e2c1a9; font-family: 'Playfair Display';">Confirm Reward</span>`,
        html: `<p style="color: #fff;">Would you like to spend <b>${cost} points</b> for <b>${rewardName}</b>?</p>`,
        showCancelButton: true,
        confirmButtonText: 'YES, REDEEM',
        cancelButtonText: 'NOT NOW',
        confirmButtonColor: '#e2c1a9',
        cancelButtonColor: 'rgba(255,255,255,0.05)',
        customClass: { popup: 'cafe-popup' }
    }).then((result) => {
        if (result.isConfirmed) {
            processRedemption(rewardName, cost);
        }
    });
}

function processRedemption(rewardName, cost) {
    const formData = new URLSearchParams();
    formData.append('reward_name', rewardName);
    formData.append('cost', cost);

    fetch('process_rewards.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Redeemed!',
                text: 'Your reward is now available to use in your vouchers.',
                background: '#1b0f0a',
                color: '#fff',
                confirmButtonColor: '#e2c1a9'
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: data.message,
                background: '#1b0f0a',
                color: '#fff'
            });
        }
    });
}
</script>

</body>
</html>