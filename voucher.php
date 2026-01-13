<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. FETCH USER DATA
$stmt = $conn->prepare("SELECT id, points, membership_level, birthday FROM customers WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$points = $user['points'] ?? 0;
$tier = $user['membership_level'] ?? 'Cloud Starter';
$birthday = $user['birthday'] ?? '';

// 2. BIRTHDAY LOGIC (Keep this for the top banner only)
$isBirthdayMonth = false;
if (!empty($birthday) && $birthday !== '0000-00-00') {
    $birthMonth = date('m', strtotime($birthday)); 
    $currentMonth = date('m'); 
    if ($birthMonth === $currentMonth) {
        $isBirthdayMonth = true;
    }
}

// 3. FETCH HISTORY
$history_stmt = $conn->prepare("SELECT voucher_name, claimed_at FROM voucher_claims WHERE user_id = ? ORDER BY claimed_at DESC LIMIT 5");
$history_stmt->bind_param("i", $user_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud Rewards | SugarCloud</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root { 
            --accent: #e2c1a9; 
            --dark-bg: #1b0f0a; 
            --card-bg: #2b1a13;
        }

        body { 
            background: var(--dark-bg); 
            font-family: 'Poppins', sans-serif; 
            color: white; 
            margin: 0; 
            overflow-x: hidden;
        }

        .container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
        
        .nav-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .home-btn { text-decoration: none; color: var(--accent); font-size: 12px; letter-spacing: 2px; text-transform: uppercase; font-weight: 500; transition: 0.3s; }

        .dashboard-hero {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 50px 20px;
            text-align: center;
            border: 1px solid rgba(226, 193, 169, 0.1);
            margin-bottom: 50px;
        }

        .points-number { 
            font-family: 'Playfair Display', serif; 
            font-size: 4rem; 
            margin: 0;
        }

        .bday-banner {
            background: linear-gradient(45deg, #4a2c1d, #2b1a13);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid var(--accent);
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(226, 193, 169, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(226, 193, 169, 0); }
            100% { box-shadow: 0 0 0 0 rgba(226, 193, 169, 0); }
        }

        .swiper { width: 100%; padding: 30px 0 60px; }
        .swiper-slide { 
            width: 280px; height: 400px; 
            border-radius: 25px; 
            overflow: hidden; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            cursor: pointer;
        }
        .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }

        .section-header {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--accent);
            margin-bottom: 30px;
            text-align: center;
        }

        .history-list { background: rgba(255,255,255,0.03); border-radius: 25px; padding: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .history-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .cafe-popup { background: #1b0f0a !important; color: white !important; border: 1px solid var(--accent) !important; border-radius: 25px !important; }
    </style>
</head>
<body>

<div class="container animate__animated animate__fadeIn">
    <div class="nav-header">
        <a href="index.php" class="home-btn"><i class="fas fa-home"></i> Back to Home</a>
    </div>

    <?php if ($isBirthdayMonth): ?>
    <div class="bday-banner animate__animated animate__bounceIn">
        <div style="font-size: 40px; color: var(--accent);"><i class="fas fa-birthday-cake"></i></div>
        <div>
            <h4 style="margin: 0; font-family: 'Playfair Display', serif;">A Gift for Your Special Month!</h4>
            <p style="margin: 5px 0 0; font-size: 12px; opacity: 0.8;">Enjoy exclusive rewards on us.</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="dashboard-hero">
        <p style="text-transform: uppercase; letter-spacing: 4px; font-size: 10px; color: var(--accent);">Cloud Points Balance</p>
        <h1 class="points-number"><?= number_format($points) ?></h1>
        <span style="border: 1px solid var(--accent); padding: 5px 20px; border-radius: 50px; font-size: 11px; color: var(--accent);">Tier: <?= $tier ?></span>
    </div>

    <div class="section-header">Exclusive Seasonal Vouchers</div>

    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            
            <div class="swiper-slide" onclick="handleRedemption('Birthday Special Reward', 'Enjoy 40% OFF any cake slice!')">
                <img src="images/voucher 1.png" alt="Birthday Special">
            </div>

            <div class="swiper-slide" onclick="handleRedemption('New Year 2026 Bonanza', 'Celebrate 2026 with 35% OFF your total bill.')">
                <img src="images/voucher 3.png" alt="New Year Discount">
            </div>

            <div class="swiper-slide" onclick="handleRedemption('Valentine Sweet Treat', '30% OFF on all dessert menu items.')">
                <img src="images/voucher 2.png" alt="Valentine Gift">
            </div>

        </div>
        <div class="swiper-pagination"></div>
    </div>

    <div class="section-header">Recent Redemptions</div>
    <div class="history-list">
        <?php if ($history_result->num_rows > 0): ?>
            <?php while($row = $history_result->fetch_assoc()): ?>
            <div class="history-row">
                <div>
                    <h5 style="margin:0;"><?= htmlspecialchars($row['voucher_name']) ?></h5>
                    <small style="opacity: 0.4;"><?= date('D, d M Y', strtotime($row['claimed_at'])) ?></small>
                </div>
                <div style="color: #2ecc71; font-size: 11px;"><i class="fas fa-check-circle"></i> Used</div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; opacity:0.3; padding: 20px;">No redemption history yet.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    function handleRedemption(title, message) {
        Swal.fire({
            title: `<span style="color: #e2c1a9; font-family: 'Playfair Display';">${title}</span>`,
            html: `<p style="color: #fff;">${message}</p>`,
            showCancelButton: true,
            confirmButtonText: 'REDEEM NOW',
            confirmButtonColor: '#e2c1a9',
            customClass: { popup: 'cafe-popup' }
        }).then((result) => {
            if (result.isConfirmed) {
                processClaim(title);
            }
        });
    }

    function processClaim(vName) {
        const formData = new URLSearchParams();
        formData.append('voucher_name', vName);

        fetch('claim_voucher.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                Swal.fire({ icon: 'success', title: 'Claimed!', background: '#1b0f0a', color: '#fff' })
                .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Oops...', text: data.message, background: '#1b0f0a', color: '#fff' });
            }
        });
    }

    var swiper = new Swiper(".mySwiper", {
        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: "auto",
        coverflowEffect: { rotate: 20, stretch: 0, depth: 150, modifier: 1, slideShadows: false },
        pagination: { el: ".swiper-pagination", clickable: true },
        autoplay: { delay: 4000 }
    });
</script>
</body>
</html>