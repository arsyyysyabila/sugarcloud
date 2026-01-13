<?php
session_start();
require_once('db.php'); 

// 1. PROTECT DASHBOARD
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login_admin.php");
    exit();
}

// 2. FETCH GENERAL STATS (Sales)
$stats_query = "SELECT SUM(total_amount) as total_revenue, COUNT(id) as total_orders FROM sales";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// 3. FETCH TOP SELLING PRODUCTS (For Chart & Table)
$sales_query = "SELECT item_name, SUM(quantity) as total_qty, SUM(total_amount) as item_revenue 
                FROM sales 
                GROUP BY item_name 
                ORDER BY total_qty DESC LIMIT 5";
$sales_result = mysqli_query($conn, $sales_query);

$chart_labels = [];
$chart_data = [];
$table_rows = "";
$no = 1;

while ($row = mysqli_fetch_assoc($sales_result)) {
    $chart_labels[] = $row['item_name'];
    $chart_data[] = $row['total_qty'];
    
    $table_rows .= "<tr>
        <td>{$no}</td>
        <td><strong>{$row['item_name']}</strong></td>
        <td><span class='qty-badge'>{$row['total_qty']}</span></td>
        <td>RM " . number_format($row['item_revenue'], 2) . "</td>
    </tr>";
    $no++;
}

// 4. FETCH PEAK RESERVATION DAYS (Updated to use 'reserve_date')
$res_query = "SELECT reserve_date, COUNT(*) as total_bookings FROM reservations 
              GROUP BY reserve_date 
              ORDER BY total_bookings DESC LIMIT 5";
$res_result = mysqli_query($conn, $res_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Analytics | SugarCloud</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --gold: #d4af7f;
            --dark-bg: #1b0f0a;
            --panel-bg: rgba(59, 44, 35, 0.6);
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #1b0f0a;
            color: #f8e8d0;
            margin: 0;
            display: flex;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: var(--sidebar-width);
            background: #000;
            height: 100vh;
            position: fixed;
            padding: 30px 20px;
            border-right: 1px solid rgba(212, 175, 127, 0.2);
            z-index: 1000;
        }

        .sidebar .brand {
            font-family: 'Great Vibes';
            font-size: 35px;
            color: var(--gold);
            text-align: center;
            margin-bottom: 50px;
            display: block;
            text-decoration: none;
        }

        .nav-menu { list-style: none; padding: 0; }
        .nav-item { margin-bottom: 10px; }
        .nav-link {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-radius: 12px;
            transition: 0.3s;
            font-size: 14px;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--gold);
            color: #000;
        }

        .nav-link i { width: 20px; text-align: center; }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        /* KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .kpi-card {
            background: var(--panel-bg);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .kpi-icon {
            font-size: 24px;
            color: var(--gold);
            background: rgba(212, 175, 127, 0.1);
            width: 50px; height: 50px;
            display: flex; justify-content: center; align-items: center;
            border-radius: 12px;
        }

        /* Panels */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .panel {
            background: var(--panel-bg);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .full-panel { grid-column: span 2; }

        .sales-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .sales-table th { text-align: left; padding: 12px; color: var(--gold); border-bottom: 1px solid rgba(212, 175, 127, 0.2); font-size: 14px; }
        .sales-table td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }

        .qty-badge { background: var(--gold); color: #000; padding: 2px 8px; border-radius: 6px; font-weight: 600; font-size: 12px; }

        @media (max-width: 1024px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .full-panel { grid-column: span 1; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="index.php" class="brand">SC</a>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i class="fas fa-chart-pie"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="menu.php" class="nav-link">
                    <i class="fas fa-utensils"></i> <span>View Menu</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="manage_orders.php" class="nav-link">
                    <i class="fas fa-shopping-bag"></i> <span>Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="reservation_list.php" class="nav-link">
                    <i class="fas fa-calendar-alt"></i> <span>Reservations</span>
                </a>
            </li>
            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">
            <li class="nav-item">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i> <span>Back to Site</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link" style="color: #ff6b6b;">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <div>
                <h1 style="margin: 0; font-size: 28px;">Analytics Overview</h1>
                <p style="color: #aaa; margin: 5px 0 0;">Monitoring SugarCloud performance</p>
            </div>
            <div class="user-info">
                <span style="color: var(--gold); font-weight: 600;">Admin:</span> <?= $_SESSION['user_name'] ?? 'SugarCloud Admin' ?>
            </div>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-coins"></i></div>
                <div class="kpi-data">
                    <h4 style="margin:0; font-size:12px; color:#aaa; letter-spacing: 1px;">TOTAL REVENUE</h4>
                    <h2 style="margin:5px 0 0;">RM <?= number_format($stats['total_revenue'] ?? 0, 2) ?></h2>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-box-open"></i></div>
                <div class="kpi-data">
                    <h4 style="margin:0; font-size:12px; color:#aaa; letter-spacing: 1px;">TOTAL ORDERS</h4>
                    <h2 style="margin:5px 0 0;"><?= $stats['total_orders'] ?? 0 ?></h2>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="panel">
                <h3 style="margin-top:0; color: var(--gold);"><i class="fas fa-fire"></i> Most Ordered Items</h3>
                <canvas id="salesChart" height="200"></canvas>
            </div>

            <div class="panel">
                <h3 style="margin-top:0; color: var(--gold);"><i class="fas fa-calendar-check"></i> Peak Reservation Days</h3>
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th style="text-align: right;">Total Bookings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($res_result) > 0): ?>
                            <?php while($res = mysqli_fetch_assoc($res_result)): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($res['reserve_date'])) ?></td>
                                <td style="text-align: right;"><span class="qty-badge"><?= $res['total_bookings'] ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="2" style="text-align:center; color:#777;">No records found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="panel full-panel">
                <h3 style="margin-top:0; color: var(--gold);"><i class="fas fa-award"></i> Best Selling Products Detail</h3>
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product Name</th>
                            <th>Sold Qty</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= $table_rows ?: "<tr><td colspan='4' style='text-align:center; color:#777;'>No sales recorded</td></tr>" ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [{
                    label: 'Units Sold',
                    data: <?= json_encode($chart_data) ?>,
                    backgroundColor: 'rgba(212, 175, 127, 0.5)',
                    borderColor: 'rgba(212, 175, 127, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#aaa' } 
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#aaa' } 
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>