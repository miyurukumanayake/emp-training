<?php
session_start();

require_once "./connection.php";

if ($_SESSION['user'] && isset($_POST['selectedPlanId'])) {
    $userId = $_SESSION['user']['id'];
    $role = $_SESSION['user']['role'];
    $planId = $_POST['selectedPlanId'];
    $sql = $role === 'admin'
        ? "SELECT id, name FROM plans WHERE id = $planId"
        : "SELECT
                planId as id,
                (SELECT plans.name FROM plans WHERE plans.id = planId) as name,
                (SELECT DATE_FORMAT(time, '%b %d, %Y') FROM plans WHERE plans.id = planId) as time,
                (SELECT `status` FROM plans WHERE plans.id = planId) as `status`
            FROM `user-plans` WHERE userId = $userId AND planId = $planId";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $_SESSION['selectedPlan'] = $result->fetch_assoc();
    }

    echo json_encode(['status' => 200]);
}