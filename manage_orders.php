<?php
session_start();
include('db.php');

// 1. Ambil semua data dari jadual sales
// Kita susun berdasarkan ID atau Tarikh yang terbaru di atas
$query = "SELECT * FROM sales ORDER BY sale_date DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sales | SugarCloud Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --gold: #d4af37;
            --accent: #e2c1a9;
            --dark-bg: #1b0f0a;
            --panel: rgba(43, 26, 19, 0.95);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--dark-bg);
            color: white;
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(226, 193, 169, 0.2);
            padding-bottom: 15px;
        }

        .header h1 { color: var(--accent); margin: 0; font-size: 24px; }

        .table-container {
            background: var(--panel);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            color: var(--accent);
            padding: 15px;
            border-bottom: 2px solid rgba(226, 193, 169, 0.1);
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 1px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 14px;
        }

        tr:hover {
            background: rgba(226, 193, 169, 0.05);
        }

        .badge-qty {
            background: var(--accent);
            color: var(--dark-bg);
            padding: 2px 10px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 12px;
        }

        .price-text { color: #50C878; font-weight: 600; }

        .back-btn {
            color: var(--accent);
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .back-btn:hover { color: white; }
    </style>
</head>
<body>

<div class="header">
    <h1><i class="fas fa-history"></i> Recent Sales Records</h1>
    <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) { 
            ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td style="font-weight: 600;"><?= $row['item_name'] ?></td>
                <td><span class="badge-qty"><?= $row['quantity'] ?></span></td>
                <td>RM <?= number_format($row['price'], 2) ?></td>
                <td><span class="price-text">RM <?= number_format($row['total_amount'], 2) ?></span></td>
                <td style="opacity: 0.7; font-size: 12px;">
                    <?= date('d M Y, h:i A', strtotime($row['sale_date'])) ?>
                </td>
            </tr>
            <?php 
                } 
            } else {
                echo "<tr><td colspan='6' style='text-align:center; padding:30px; opacity:0.5;'>No sales recorded yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>