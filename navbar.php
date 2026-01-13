<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<header class="main-navbar animate__animated animate__fadeInDown">
    <div class="nav-content">
        <a href="index.php" class="nav-brand">SugarCloudCafe</a>

        <nav>
            <ul class="nav-menu">
                <li><a href="index.php#home">Home</a></li>
                <li><a href="index.php#about">About</a></li>
                <li><a href="menu.php">Menu</a></li>
                
                <li><a href="rewards.php"><i class="fas fa-star" style="font-size: 0.8rem; color: var(--gold);"></i> Rewards</a></li>
                <li><a href="voucher.php"><i class="fas fa-ticket-alt" style="font-size: 0.8rem; color: var(--gold);"></i> Vouchers</a></li>
                
                <li><a href="reservation.php">Reservation</a></li>

                <?php if(isset($_SESSION['user_id']) || isset($_SESSION['is_logged_in'])): ?>
                    <li class="nav-separator"></li>
                    
                    <li>
                        <a href="profile.php" class="profile-pill">
                            <i class="fas fa-user-circle"></i> Profile
                        </a>
                    </li>

                    <li>
                        <a href="cart.php" class="cart-wrapper">
                            <i class="fas fa-shopping-basket"></i>
                            <?php if(!empty($_SESSION['cart'])): ?>
                                <span class="cart-dot"><?= count($_SESSION['cart']) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <li>
                        <a href="logout.php" class="logout-icon" title="Logout">
                            <i class="fas fa-power-off"></i>
                        </a>
                    </li>

                <?php else: ?>
                    <li><a href="login.php" class="login-pill">Log In</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<style>
    :root {
        --gold: #e2c1a9;
        --dark: #1b0f0a;
        --glass: rgba(27, 15, 10, 0.85);
        --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Navbar Container */
    .main-navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 2000;
        background: var(--glass);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-bottom: 1px solid rgba(226, 193, 169, 0.1);
        padding: 12px 0;
    }

    .nav-content {
        max-width: 1250px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 5%;
    }

    /* Brand Logo Style */
    .nav-brand {
        font-family: "Great Vibes", cursive;
        font-size: 2.4rem;
        color: var(--gold);
        text-decoration: none;
        transition: var(--transition);
    }

    .nav-brand:hover {
        text-shadow: 0 0 15px rgba(226, 193, 169, 0.6);
        transform: scale(1.02);
    }

    /* Menu List */
    .nav-menu {
        list-style: none;
        display: flex;
        align-items: center;
        gap: 22px;
        margin: 0;
        padding: 0;
    }

    .nav-menu li a {
        text-decoration: none;
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.85rem;
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .nav-menu li a:hover {
        color: var(--gold);
    }

    /* Separator Line */
    .nav-separator {
        width: 1px;
        height: 24px;
        background: rgba(226, 193, 169, 0.2);
        margin: 0 8px;
    }

    /* Profile Capsule Button */
    .profile-pill {
        background: rgba(226, 193, 169, 0.08);
        border: 1px solid rgba(226, 193, 169, 0.25);
        padding: 8px 18px !important;
        border-radius: 50px;
        color: var(--gold) !important;
    }

    .profile-pill:hover {
        background: var(--gold);
        color: var(--dark) !important;
        box-shadow: 0 4px 15px rgba(226, 193, 169, 0.2);
    }

    /* Cart Wrapper & Badge */
    .cart-wrapper {
        position: relative;
        font-size: 1.15rem !important;
    }

    .cart-dot {
        position: absolute;
        top: -10px;
        right: -12px;
        background: var(--gold);
        color: var(--dark);
        font-size: 10px;
        font-weight: 900;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    /* Action Buttons */
    .login-pill {
        background: var(--gold);
        color: var(--dark) !important;
        padding: 10px 24px !important;
        border-radius: 30px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem !important;
    }

    .logout-icon {
        color: #ff6b6b !important;
        font-size: 1.1rem !important;
        margin-left: 8px;
    }

    .logout-icon:hover {
        transform: rotate(90deg) scale(1.1);
        color: #ff4757 !important;
    }

    /* Mobile Responsive */
    @media (max-width: 1024px) {
        .nav-menu li:not(.cart-wrapper):not(.profile-pill):not(.logout-icon) {
            display: none; 
        }
        .nav-brand { font-size: 1.9rem; }
    }
</style>