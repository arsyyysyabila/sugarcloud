<?php
session_start();
require_once('db.php'); 


// Check if user is logged in
$isLoggedIn = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

// --- POINT & TIER LOGIC ---
$userPoints = 0;
$tier = "Guest";
$tierColor = "#888";
$nextTierPoints = 100;

if ($isLoggedIn) {
    $userPoints = $_SESSION['points'] ?? 0;

    if ($userPoints >= 500) {
        $tier = "Cloud Elite";
        $tierColor = "#D4AF37"; 
        $nextTierPoints = 1000; // Cap
    } elseif ($userPoints >= 100) {
        $tier = "Sugar Lover";
        $tierColor = "#FF69B4"; 
        $nextTierPoints = 500;
    } else {
        $tier = "Cloud Starter";
        $tierColor = "#87CEEB"; 
        $nextTierPoints = 100;
    }
}

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

/* MENU DATA */
$desserts = [
    ["name"=>"Chocolate Brownie","price"=>10,"img"=>"chocolate brownie.jpg"],
    ["name"=>"Cookies","price"=>5,"img"=>"cookies.jpg"],
    ["name"=>"Apple Pie","price"=>19,"img"=>"apple pie 1.jpg"],
    ["name"=>"Tiramisu","price"=>18,"img"=>"tiramisu.jpg"],
    ["name"=>"Chocolate Mousse","price"=>23,"img"=>"chocolate mousse.jpg"],
    ["name"=>"Creme Brulee","price"=>25,"img"=>"creme  brule.jpg"],
    ["name"=>"Matcha Cupcake","price"=>9,"img"=>"matcha cup.jpg"],
    ["name"=>"Pudding","price"=>9,"img"=>"pudding.jpg"],
    ["name"=>"Ice Cream","price"=>4,"img"=>"ice cream.jpg"],
    ["name"=>"Cheese Cake","price"=>11,"img"=>"cheese cake.jpg"]
];

$drinks = [
    ["name"=>"Americano","price"=>8,"img"=>"americano.jpg"],
    ["name"=>"Cappuccino","price"=>10,"img"=>"cappucino.jpg"],
    ["name"=>"Latte","price"=>11,"img"=>"latte.jpg"],
    ["name"=>"Mocha","price"=>12,"img"=>"mocha.jpg"],
    ["name"=>"Caramel Macchiato","price"=>13,"img"=>"caramel macchiato.jpg"],
    ["name"=>"Orange Juice","price"=>7,"img"=>"orange juice.jpg"],
    ["name"=>"Apple Juice","price"=>7,"img"=>"apple juice.jpg"],
    ["name"=>"Lemon Soda","price"=>6,"img"=>"lemon soda.jpg"],
    ["name"=>"Strawberry Soda","price"=>6,"img"=>"strawberry soda.jpg"],
    ["name"=>"Mango Smoothie","price"=>9,"img"=>"mango smoothie.jpg"]
];

/* ADD TO CART LOGIC */
if (isset($_POST["add"]) && $isLoggedIn) {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $qty = (int)$_POST["qty"];

    if ($qty > 0) {
        if (isset($_SESSION["cart"][$name])) {
            $_SESSION["cart"][$name]["quantity"] += $qty;
        } else {
            $_SESSION["cart"][$name] = [
                "name" => $name,
                "price" => $price,
                "quantity" => $qty
            ];
        }
    }
}

$totalQty = 0;
foreach ($_SESSION["cart"] as $item) {
    $totalQty += $item["quantity"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | SugarCloudCafe</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <?php include('navbar.php'); ?>
    <style>
        :root {
            --accent: #e2c1a9;
            --bg-dark: #1b0f0a;
            --glass: rgba(255, 255, 255, 0.08);
            --gold: #d4af7f;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(rgba(27, 15, 10, 0.9), rgba(27, 15, 10, 0.9)), url('images/bg3.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            scroll-behavior: smooth;
        }

        

        /* PROFILE CARD SECTION (STARBUCKS STYLE) */
        .profile-container {
            padding: 120px 5% 40px;
            display: flex;
            justify-content: center;
        }

        .profile-card {
            background: rgba(43, 26, 19, 0.6);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            border: 1px solid rgba(212, 175, 127, 0.2);
            padding: 35px;
            width: 100%;
            max-width: 700px;
            display: flex;
            align-items: center;
            gap: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
        }

        .profile-card::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(212, 175, 127, 0.05) 0%, transparent 70%);
            z-index: 0;
        }

        .profile-left { position: relative; z-index: 1; }
        
        .pfp-wrapper {
            width: 130px; height: 130px;
            border-radius: 50%;
            border: 4px solid var(--gold);
            padding: 5px;
            background: var(--bg-dark);
            position: relative;
            cursor: pointer;
            transition: 0.3s;
        }

        .pfp-wrapper:hover { transform: scale(1.05); box-shadow: 0 0 20px rgba(212, 175, 127, 0.3); }

        .pfp-wrapper img {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .edit-pfp {
            position: absolute; bottom: 5px; right: 5px;
            background: var(--gold); color: #000;
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            font-size: 14px; border: 3px solid #2b1a13;
        }

        .profile-right { flex: 1; z-index: 1; }
        
        .tier-tag {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            color: #000;
        }

        .user-name { font-size: 28px; font-weight: 600; margin-bottom: 5px; }
        .points-display { font-size: 14px; color: var(--accent); margin-bottom: 20px; }
        .points-display strong { font-size: 24px; color: #fff; }

        /* Progress Bar */
        .progress-box { width: 100%; }
        .progress-text { display: flex; justify-content: space-between; font-size: 11px; color: #aaa; margin-bottom: 8px; }
        .progress-bg { height: 8px; background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; }
        .progress-fill { 
            height: 100%; 
            background: linear-gradient(90deg, var(--gold), #fff); 
            border-radius: 10px;
            transition: width 1.5s cubic-bezier(0.1, 0, 0.3, 1);
        }

        /* Container & Menu */
        .container { width: 90%; max-width: 1200px; margin: auto; padding: 40px 0 80px; }
        h1 { font-family: "Great Vibes"; font-size: 65px; text-align: center; color: var(--gold); margin-bottom: 30px; }
        h2 { font-size: 28px; border-left: 4px solid var(--gold); padding-left: 15px; margin: 40px 0 25px; text-transform: uppercase; letter-spacing: 2px; }

        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px; }
        .menu-card {
            background: var(--glass); backdrop-filter: blur(5px); border-radius: 20px; overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1); transition: 0.3s ease; display: flex; flex-direction: column;
        }
        .menu-card:hover { transform: translateY(-10px); border-color: var(--gold); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .menu-card img { width: 100%; height: 220px; object-fit: cover; }
        .card-info { padding: 20px; text-align: center; }
        .price { color: var(--gold); font-weight: 600; margin-bottom: 15px; }

        .cart-box { display: flex; justify-content: center; gap: 10px; align-items: center; }
        .qty-input { width: 50px; padding: 8px; border-radius: 8px; border: none; background: rgba(0,0,0,0.3); color: #fff; text-align: center; }
        .add-btn { background: var(--gold); color: #1b0f0a; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .add-btn:hover { background: #fff; transform: scale(1.05); }

        /* Floating Cart */
        .floating-cart {
            position: fixed; bottom: 30px; right: 30px; background: var(--gold); 
            width: 65px; height: 65px; border-radius: 50%; display: flex; 
            justify-content: center; align-items: center; text-decoration: none; font-size: 24px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4); z-index: 999;
        }
        .cart-count {
            position: absolute; top: 0; right: 0; background: #ff4757; color: white;
            width: 24px; height: 24px; border-radius: 50%; display: flex; 
            justify-content: center; align-items: center; font-size: 11px; font-weight: bold; border: 2px solid var(--bg-dark);
        }

        /* Modals */
        .modal-overlay {
            display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.9); backdrop-filter: blur(10px); justify-content: center; align-items: center;
        }
        .modal-card {
            background: #2b1a13; padding: 45px; border-radius: 30px; text-align: center;
            border: 1px solid rgba(212, 175, 127, 0.3); width: 90%; max-width: 420px;
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes popIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .popup-btn { display: inline-block; padding: 12px 30px; border-radius: 12px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; margin-top: 20px; }
        .popup-btn.primary { background: var(--gold); color: #1b0f0a; width: 100%; }

        @media (max-width: 600px) {
            .profile-card { flex-direction: column; text-align: center; padding: 30px 20px; }
            .pfp-wrapper { width: 100px; height: 100px; margin: 0 auto; }
        }
    </style>
</head>
<body>

    

    <?php if($isLoggedIn): ?>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-left">
                <div class="pfp-wrapper" onclick="document.getElementById('imageUpload').click()">
                    <?php 
                        $profileImg = isset($_SESSION['pfp']) ? $_SESSION['pfp'] : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
                    ?>
                    <img src="<?= $profileImg ?>" id="pfpDisplay" alt="User Profile">
                    <div class="edit-pfp">📷</div>
                </div>
                <input type="file" id="imageUpload" style="display:none;" accept="image/*" onchange="previewImage(event)">
            </div>

            <div class="profile-right">
                <div class="tier-tag" style="background: <?= $tierColor ?>;"><?= $tier ?> Member</div>
                <h3 class="user-name">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h3>
                <div class="points-display">🌟 <strong><?= number_format($userPoints) ?></strong> Points Collected</div>
                
                <div class="progress-box">
                    <div class="progress-text">
                        <span>Progress to Reward</span>
                        <span><?= $userPoints ?> / <?= $nextTierPoints ?></span>
                    </div>
                    <div class="progress-bg">
                        <?php $percent = ($userPoints / $nextTierPoints) * 100; ?>
                        <div class="progress-fill" style="width: <?= min($percent, 100) ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if($isLoggedIn && $totalQty > 0): ?>
    <a href="cart.php" class="floating-cart">🛒 <span class="cart-count"><?= $totalQty ?></span></a>
    <?php endif; ?>

    <div class="container">
        <h1>Our Sweet Menu</h1>
        
        <h2>🍰 Exquisite Desserts</h2>
        <div class="menu-grid">
            <?php foreach ($desserts as $d): ?>
            <div class="menu-card">
                <img src="images/<?= $d["img"] ?>" alt="<?= $d["name"] ?>">
                <div class="card-info">
                    <h3><?= $d["name"] ?></h3>
                    <p class="price">RM <?= number_format($d["price"], 2) ?></p>

                    <form method="post" class="cart-box">
                        <input type="hidden" name="name" value="<?= $d["name"] ?>">
                        <input type="hidden" name="price" value="<?= $d["price"] ?>">
                        <input type="number" name="qty" value="1" min="1" class="qty-input">
                        
                        <?php if($isLoggedIn): ?>
                            <button type="submit" name="add" class="add-btn">Add</button>
                        <?php else: ?>
                            <button type="button" class="add-btn" onclick="showLoginPopup()">Add</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <h2 style="margin-top: 60px;">☕ Signature Drinks</h2>
        <div class="menu-grid">
            <?php foreach ($drinks as $d): ?>
            <div class="menu-card">
                <img src="images/<?= $d["img"] ?>" alt="<?= $d["name"] ?>">
                <div class="card-info">
                    <h3><?= $d["name"] ?></h3>
                    <p class="price">RM <?= number_format($d["price"], 2) ?></p>

                    <form method="post" class="cart-box">
                        <input type="hidden" name="name" value="<?= $d["name"] ?>">
                        <input type="hidden" name="price" value="<?= $d["price"] ?>">
                        <input type="number" name="qty" value="1" min="1" class="qty-input">
                        
                        <?php if($isLoggedIn): ?>
                            <button type="submit" name="add" class="add-btn">Add</button>
                        <?php else: ?>
                            <button type="button" class="add-btn" onclick="showLoginPopup()">Add</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="loginPopup" class="modal-overlay">
        <div class="modal-card">
            <h2 style="font-family: 'Poppins'; font-size: 24px; color: var(--gold);">Exclusive Member Access</h2>
            <p style="font-weight: 300; font-size: 14px; color: #eee; margin-top: 10px;">
                Join our club to start ordering and earning sweet rewards!
            </p>
            <a href="login.php" class="popup-btn primary">Login / Register</a>
            <p onclick="closeLoginPopup()" style="color:#888; cursor:pointer; font-size:12px; margin-top:15px;">Maybe later</p>
        </div>
    </div>

    <?php if (isset($_SESSION['login_success'])): ?>
    <div id="welcomePopup" class="modal-overlay" style="display: flex;">
        <div class="modal-card">
            <div style="font-size: 50px; margin-bottom: 10px;">☁️</div>
            <span style="font-family: 'Great Vibes'; font-size: 40px; color: var(--gold);">Welcome Back!</span>
            <p style="font-size: 16px; margin: 15px 0;">Hi <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>!</p>
            <p style="font-weight: 300; font-size: 13px; color: #aaa; margin-bottom: 20px;">
                Your current tier: <strong style="color:<?= $tierColor ?>"><?= $tier ?></strong>
            </p>
            <button class="popup-btn primary" onclick="closeWelcomePopup()">Start Sweet Journey</button>
        </div>
    </div>
    <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>

    <script>
        function showLoginPopup() { document.getElementById('loginPopup').style.display = 'flex'; }
        function closeLoginPopup() { document.getElementById('loginPopup').style.display = 'none'; }
        function closeWelcomePopup() { document.getElementById('welcomePopup').style.display = 'none'; }
        
        // Image Preview Script
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('pfpDisplay');
                output.src = reader.result;
                // Note: To save this permanently, you'd need an AJAX call to save the base64 or file to the server.
                alert("Looking good! Your profile picture has been updated.");
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        window.onclick = function(e) { 
            if (e.target.className === 'modal-overlay') {
                closeLoginPopup();
                closeWelcomePopup();
            }
        }
    </script>
</body>
</html>