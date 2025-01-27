<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;
$config = $_SESSION['config'];

if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit();
} elseif ($_SESSION['user']['role'] === 'admin') {
    header('Location: /admin');
    exit();
}