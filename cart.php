<?php
session_start();

// Update Quantity Logic
if (isset($_POST['update_qty'])) {
    $name = $_POST['item_name'];
    $action = $_POST['action'];
    
    if (isset($_SESSION['cart'][$name])) {
        if ($action == 'plus') {
            $_SESSION['cart'][$name]['quantity']++;
        } else if ($action == 'minus') {
            $_SESSION['cart'][$name]['quantity']--;
            if ($_SESSION['cart'][$name]['quantity'] < 1) {
                unset($_SESSION['cart'][$name]);
            }
        }
    }
    header("Location: cart.php");
    exit();
}

// Remove Item Logic
if (isset($_POST['remove_item'])) {
    $name = $_POST['item_name'];
    unset($_SESSION['cart'][$name]);
    header("Location: cart.php");
    exit();
}

$totalPrice = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart | SugarCloudCafe</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --accent: #e2c1a9;
            --bg-dark: #1b0f0a;
            --card-bg: rgba(255, 255, 255, 0.05);
        }

        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(rgba(27, 15, 10, 0.9), rgba(27, 15, 10, 0.9)), url('images/bg3.jpg');
            background-size: cover;
            background-attachment: fixed;
            color: white;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Navigation */
        .cart-header {
            padding: 30px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand { font-family: "Great Vibes"; font-size: 32px; color: var(--accent); text-decoration: none; }

        h1 {
            text-align: center;
            font-family: "Great Vibes";
            font-size: 55px;
            color: var(--accent);
            margin: 20px 0;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
        }

        /* Cart Container */
        .cart-wrapper {
            width: 90%;
            max-width: 800px;
            margin: 0 auto 150px;
            flex-grow: 1;
        }

        .cart-item {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .cart-item:hover {
            border-color: var(--accent);
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .item-info h3 { margin: 0; font-size: 1.2rem; letter-spacing: 1px; font-weight: 400; }
        .item-info p { margin: 5px 0 0; color: var(--accent); font-weight: 600; font-size: 1.1rem; }

        .qty-controls {
            display: flex;
            align-items: center;
            background: rgba(0,0,0,0.4);
            border-radius: 30px;
            padding: 5px 18px;
            gap: 15px;
            border: 1px solid rgba(226, 193, 169, 0.2);
        }

        .qty-btn {
            background: none;
            border: none;
            color: var(--accent);
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
        }

        .qty-btn:hover { color: white; transform: scale(1.2); }

        .remove-link {
            background: none;
            border: none;
            color: #ff5e5e;
            cursor: pointer;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-left: 25px;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .remove-link:hover { color: #ff2e2e; text-decoration: underline; }

        /* Checkout Bar */
        .checkout-bar {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: rgba(27, 15, 10, 0.98);
            backdrop-filter: blur(20px);
            padding: 25px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            border-top: 1px solid rgba(226, 193, 169, 0.3);
            z-index: 100;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.5);
        }

        .total-section h3 { margin: 0; font-size: 0.8rem; opacity: 0.6; text-transform: uppercase; letter-spacing: 2px; }
        .total-section p { margin: 0; font-size: 2.2rem; color: var(--accent); font-weight: 600; }

        .checkout-btn {
            background: var(--accent);
            color: #1b0f0a;
            padding: 18px 50px;
            border-radius: 40px;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(226, 193, 169, 0.2);
        }

        .checkout-btn:hover {
            background: white;
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        /* Empty Cart State */
        .empty-cart {
            text-align: center;
            padding: 80px 20px;
            background: var(--card-bg);
            border-radius: 30px;
            backdrop-filter: blur(10px);
        }
        .empty-cart p { font-size: 1.2rem; opacity: 0.7; margin-bottom: 20px; }

        .btn-back {
            display: inline-block;
            color: var(--accent);
            text-decoration: none;
            border: 1px solid var(--accent);
            padding: 12px 30px;
            border-radius: 30px;
            transition: 0.3s;
            font-weight: 500;
        }

        .btn-back:hover { background: var(--accent); color: #1b0f0a; }

        @media (max-width: 600px) {
            .cart-item { flex-direction: column; text-align: center; gap: 20px; }
            .checkout-bar { flex-direction: column; gap: 20px; text-align: center; }
            .remove-link { margin-left: 0; margin-top: 10px; }
        }
    </style>
</head>
<body>

<header class="cart-header">
    <a href="index.php" class="brand">SugarCloudCafe</a>
    <a href="menu.php" class="btn-back">← Continue Shopping</a>
</header>

<div class="container">
    <h1>Your Sweet Selection</h1>

    <div class="cart-wrapper">
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <p>Your cloud is currently empty... ☁️</p>
                <a href="menu.php" class="btn-back">Browse Our Menu</a>
            </div>
        <?php else: ?>
            <?php foreach ($_SESSION['cart'] as $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $totalPrice += $subtotal;
            ?>
                <div class="cart-item">
                    <div class="item-info">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p>RM <?= number_format($item['price'], 2) ?></p>
                    </div>

                    <div style="display: flex; align-items: center; flex-wrap: wrap; justify-content: center;">
                        <form method="POST" class="qty-controls">
                            <input type="hidden" name="item_name" value="<?= htmlspecialchars($item['name']) ?>">
                            <input type="hidden" name="action" value="">
                            
                            <button type="submit" name="update_qty" class="qty-btn" onclick="this.form.action.value='minus'">−</button>
                            <span style="min-width: 20px; text-align: center; font-weight: 600;"><?= $item['quantity'] ?></span>
                            <button type="submit" name="update_qty" class="qty-btn" onclick="this.form.action.value='plus'">+</button>
                        </form>

                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="item_name" value="<?= htmlspecialchars($item['name']) ?>">
                            <button type="submit" name="remove_item" class="remove-link">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($_SESSION['cart'])): ?>
    <div class="checkout-bar">
        <div class="total-section">
            <h3>Estimated Total</h3>
            <p>RM <?= number_format($totalPrice, 2) ?></p>
        </div>
        <button class="checkout-btn" onclick="window.location.href='payment.php'">
            Checkout Now
        </button>
    </div>
<?php endif; ?>

</body>
</html>