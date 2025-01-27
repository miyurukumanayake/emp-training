<?php
/** @noinspection PhpIncludeInspection */
require_once '../server/connection.php';

$error = null;

if (isset($_POST['deleteUser'])) {
    if ($_POST['userId'] !== $_SESSION['user']['id']) {
        $userId = $_POST['userId'];
        $sql = "DELETE FROM users WHERE id = $userId";
        if ($conn->query($sql) !== TRUE) {
            $error = "Error deleting" . $role === 'admin' ? 'admin user' : 'employee';
            $errorDetails = $conn->error;
            echo "<script>console.log(\"$errorDetails\")</script>";
        } else {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $error = "You can't delete yourself";
    }
}

if (isset($_POST['changeStatus'])) {
    if ($_POST['userId'] !== $_SESSION['user']['id']) {
        $userId = $_POST['userId'];
        $status = $_POST['status'];
        $sql = "UPDATE users SET status = '$status' WHERE id = $userId";

        if ($conn->query($sql) !== TRUE) {
            $error = "Error changing user status";
            $errorDetails = $conn->error;
            echo "<script>console.log(\"$errorDetails\")</script>";
        } else {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $error = "You can't deactivate yourself";
    }

}