<?php
session_start();
include('db.php');

// 1. Sekatan: Jika cart kosong, hantar balik ke menu
if (empty($_SESSION['cart'])) {
    header("Location: menu.php");
    exit();
}

// 2. Kira Jumlah Besar Keseluruhan
$totalAmount = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

// 3. Proses Pesanan
$orderSuccess = false;
$earnedPoints = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['process_order'])) {
    $method = $_POST['payment_method']; 
    $user_id = $_SESSION['user_id'] ?? null; 
    $status = ($method == 'Cash') ? 'Unpaid' : 'Paid';

    mysqli_begin_transaction($conn);

    try {
        // A. Simpan ke jadual sales
        foreach ($_SESSION['cart'] as $item) {
            $item_name = $item['name'];
            $qty = $item['quantity'];
            $price = $item['price'];
            $total_item_amount = $price * $qty;

            $query_sales = "INSERT INTO sales (item_name, quantity, price, total_amount) VALUES (?, ?, ?, ?)";
            $stmt_sales = mysqli_prepare($conn, $query_sales);
            mysqli_stmt_bind_param($stmt_sales, "sidd", $item_name, $qty, $price, $total_item_amount);
            mysqli_stmt_execute($stmt_sales);
        }

        // B. Rekod Pembayaran
        $temp_id = rand(1000, 9999);
        $query_pay = "INSERT INTO payments (order_id, amount, method, status) VALUES (?, ?, ?, ?)";
        $stmt_pay = mysqli_prepare($conn, $query_pay);
        mysqli_stmt_bind_param($stmt_pay, "idss", $temp_id, $totalAmount, $method, $status);
        mysqli_stmt_execute($stmt_pay);

        // C. Logik Mata Ganjaran
        if ($user_id) {
            $earnedPoints = floor($totalAmount);
            mysqli_query($conn, "UPDATE customers SET points = points + $earnedPoints WHERE id = $user_id");
            
            // Re-fetch points untuk Tier
            $res = mysqli_query($conn, "SELECT points FROM customers WHERE id = $user_id");
            $row = mysqli_fetch_assoc($res);
            $newPoints = $row['points'];
            
            $tier = "Cloud Starter";
            if ($newPoints >= 500) $tier = "Cloud Elite";
            else if ($newPoints >= 100) $tier = "Sugar Lover";

            mysqli_query($conn, "UPDATE customers SET membership_level = '$tier' WHERE id = $user_id");
            $_SESSION['points'] = $newPoints;
            $_SESSION['member_level'] = $tier;
        }

        mysqli_commit($conn);
        unset($_SESSION['cart']);
        $orderSuccess = true;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment | SugarCloudCafe</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --accent: #e2c1a9;
            --bg-dark: #1b0f0a;
            --glass: rgba(43, 26, 19, 0.95);
        }

        body {
            background: var(--bg-dark) url('images/bg3.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .payment-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(226, 193, 169, 0.2);
            border-radius: 30px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            text-align: center;
        }

        .header i { font-size: 40px; color: var(--accent); margin-bottom: 15px; }
        .header h2 { margin: 0; font-weight: 600; letter-spacing: 1px; }

        .price-box {
            background: rgba(0,0,0,0.3);
            border-radius: 20px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid var(--accent);
        }

        .price-box .label { font-size: 13px; opacity: 0.7; text-transform: uppercase; }
        .price-box .amount { font-size: 32px; font-weight: 600; color: var(--accent); }

        .method-option {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .method-option:hover { background: rgba(226, 193, 169, 0.1); border-color: var(--accent); }
        .method-option input { margin-right: 15px; accent-color: var(--accent); width: 18px; height: 18px; }

        .btn-confirm {
            background: var(--accent);
            color: var(--bg-dark);
            border: none;
            width: 100%;
            padding: 18px;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 15px;
        }

        .btn-confirm:hover { background: #fff; transform: translateY(-3px); }

        #loading-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--accent);
            border-radius: 50%;
            width: 50px; height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="payment-card" id="mainCard">
    <div class="header">
        <i class="fas fa-shield-alt"></i>
        <h2>Checkout</h2>
        <p style="font-size: 13px; opacity: 0.6;">Secure Payment Gateway</p>
    </div>

    <div class="price-box">
        <div class="label">Total Amount</div>
        <div class="amount">RM <?= number_format($totalAmount, 2) ?></div>
    </div>

    <form id="payForm" method="POST">
        <div style="text-align: left; font-size: 12px; color: var(--accent); margin-bottom: 10px; font-weight: 600;">PAYMENT METHOD</div>
        
        <label class="method-option">
            <input type="radio" name="payment_method" value="Online Banking" required>
            <div style="text-align: left;">
                <div style="font-weight: 600;">Online Banking</div>
                <small style="opacity: 0.6;">FPX / QR Pay</small>
            </div>
        </label>

        <label class="method-option">
            <input type="radio" name="payment_method" value="Cash" required>
            <div style="text-align: left;">
                <div style="font-weight: 600;">Cash at Counter</div>
                <small style="opacity: 0.6;">Pay upon arrival</small>
            </div>
        </label>

        <input type="hidden" name="process_order" value="1">
        <button type="submit" class="btn-confirm">Place Order Now</button>
    </form>

    <a href="cart.php" style="display: block; margin-top: 20px; color: #aaa; text-decoration: none; font-size: 13px;">
        <i class="fas fa-arrow-left"></i> Back to Cart
    </a>
</div>

<div id="loading-overlay">
    <div class="loader"></div>
    <p style="margin-top: 20px; color: var(--accent); letter-spacing: 1px;">Processing Secure Transaction...</p>
</div>

<script>
    // Logic for loading and success popup
    const payForm = document.getElementById('payForm');
    
    <?php if ($orderSuccess): ?>
        Swal.fire({
            title: 'Payment Successful!',
            text: 'Your order has been recorded in our sales database. You earned <?= $earnedPoints ?> points!',
            icon: 'success',
            background: '#2b1a13',
            color: '#fff',
            confirmButtonColor: '#e2c1a9',
            confirmButtonText: 'Great!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php';
            }
        });
    <?php endif; ?>

    payForm.addEventListener('submit', function() {
        document.getElementById('loading-overlay').style.display = 'flex';
    });
</script>

</body>
</html>