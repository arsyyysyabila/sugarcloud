<?php
session_start();
require_once('db.php');

// Proteksi Admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login_admin.php");
    exit();
}

// Ambil data tempahan terbaru
$query = "SELECT * FROM reservations ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Records | SugarCloud</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gold: #d4af7f; --dark-bg: #1b0f0a; --panel-bg: rgba(59, 44, 35, 0.6); }
        body { font-family: 'Poppins', sans-serif; background: var(--dark-bg); color: #f8e8d0; margin: 0; padding: 40px; }
        .container { max-width: 1100px; margin: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-back { color: var(--gold); text-decoration: none; border: 1px solid var(--gold); padding: 8px 15px; border-radius: 8px; transition: 0.3s; }
        .btn-back:hover { background: var(--gold); color: #000; }
        
        .table-container { background: var(--panel-bg); padding: 25px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: var(--gold); padding: 15px; border-bottom: 1px solid rgba(212,175,127,0.2); }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }
        
        .status-pending { color: #ffd700; background: rgba(255, 215, 0, 0.1); padding: 4px 10px; border-radius: 6px; }
        .status-approved { color: #00ff88; background: rgba(0, 255, 136, 0.1); padding: 4px 10px; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calendar-check"></i> Reservation Records</h1>
            <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Date & Time</th>
                        <th>Guests</th>
                        <th>Phone</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['full_name']) ?></strong></td>
                            <td><?= date('d M Y', strtotime($row['reserve_date'])) ?> <br> <small><?= $row['reserve_time'] ?></small></td>
                            <td><?= $row['guests'] ?> Pax</td>
                            <td><?= $row['phone'] ?></td>
                            <td><span class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">No reservations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>