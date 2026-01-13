<?php
session_start();
include('db.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $reward_name = trim($_POST['reward_name']);
    $cost = intval($_POST['cost']);

    try {
        $conn->begin_transaction();

        // 1. Fetch current points to verify again
        $stmt = $conn->prepare("SELECT points FROM customers WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || $user['points'] < $cost) {
            throw new Exception("You do not have enough points.");
        }

        // 2. Deduct points from user
        $update_stmt = $conn->prepare("UPDATE customers SET points = points - ? WHERE id = ?");
        $update_stmt->bind_param("ii", $cost, $user_id);
        $update_stmt->execute();

        // 3. Log the reward as a 'claim' so it appears in voucher history
        $log_stmt = $conn->prepare("INSERT INTO voucher_claims (user_id, voucher_name, claimed_at) VALUES (?, ?, NOW())");
        $log_stmt->bind_param("is", $user_id, $reward_name);
        $log_stmt->execute();

        $conn->commit();
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
$conn->close();
?>