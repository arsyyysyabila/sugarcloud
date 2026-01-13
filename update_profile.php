<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $phonenum = trim($_POST['phonenum']);
    $birthday = $_POST['birthday'];

    $stmt = $conn->prepare("UPDATE customers SET name = ?, phonenum = ?, birthday = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $phonenum, $birthday, $user_id);
    
    $status = ($stmt->execute()) ? "success" : "error";
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Updating...</title>
    <style>
        body { background: #1b0f0a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: #e2c1a9; font-family: sans-serif; }
        .spinner { 
            width: 40px; height: 40px; 
            border: 3px solid rgba(226, 193, 169, 0.1); 
            border-top-color: #e2c1a9; 
            border-radius: 50%; 
            animation: spin 0.8s infinite linear; 
            margin-bottom: 20px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div style="text-align:center;">
        <div class="spinner" style="margin: 0 auto 20px;"></div>
        <p style="letter-spacing: 2px; font-size: 0.8rem;">REFINING PROFILE...</p>
    </div>
    <script>
        setTimeout(() => { window.location.href = 'profile.php?status=<?= $status ?>'; }, 1000);
    </script>
</body>
</html>