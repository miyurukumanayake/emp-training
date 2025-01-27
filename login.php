<?php
session_start();

require_once 'server/connection.php';

$error = null;

$sql = "SELECT value FROM config WHERE `key`='logo'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$logo = $row['value'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (isset($email) && $email && isset($password) && $password) {
        $sql = "SELECT * FROM config";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $config = [];
            while ($row = $result->fetch_assoc()) {
                $config[$row['key']] = $row['value'];
            }
            $_SESSION['config'] = $config;
        }

        $hashedPassword = md5($password);
        $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$hashedPassword'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($user['status'] === 'inactive') {
                $error = 'Your account is inactive';
            } else {
                $sql = "SELECT status FROM plans p, `user-plans` up WHERE p.id = up.planId AND userId = " . $user['id'];
                $result = $conn->query($sql);

                if ($user['role'] === 'admin' || $result->num_rows > 0) {
                    $_SESSION['user'] = $user;

                    if ($user['role'] === 'admin') {
                        header('Location: /admin');
                        exit();
                    }

                    $hasActivePlan = false;
                    while ($row = $result->fetch_assoc()) {
                        if ($row['status'] === 'active') {
                            $hasActivePlan = true;
                            break;
                        }
                    }

                    if ($hasActivePlan) {
                        header('Location: /');
                        exit();
                    } else {
                        $error = "You dont have any active plans";
                    }
                } else {
                    $error = "You dont have any plans yet";
                }
            }
        } else {
            $error = 'Invalid email or password';
        }
    } else {
        $error = 'Email and password are required';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/menu.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel='stylesheet' href='login.css'>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>

    <title id="title"></title>
</head>
<body>
<div id="app">
    <div class="login-page d-flex align-items-center justify-content-center h-100 p-5">
        <div class="info-col d-flex flex-column align-items-center text-white text-center">
            <h1 class="fw-bold mb-3">Designed for your success</h1>
            <h3 class="mb-5">A step-by-step plan to guide you<br>through your career journey.</h3>
            <h5 class="rounded-4 p-4" style="background-color: #0000aa50">
                Your personalized guide to mastering skills, advancing your career,<br>and achieving your goals
            </h5>
        </div>
        <div class="login-col">
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" class="login-form rounded-5">
                <?php if ($logo) { ?>
                    <div class="d-flex justify-content-center mb-4">
                        <img src="/<?php echo $logo ?>" alt="Logo" style="width: 80%;">
                    </div>
                <?php } ?>
                <h2 class="mb-4 text-center fw-bold">Login</h2>
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email address</label>
                    <input id="email" type="email" name="email" class="form-control form-control-lg">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <input id="password" type="password" name="password" class="form-control form-control-lg">
                </div>
                <button name="submit" class="btn btn-primary mb-3 w-100 btn-lg">Login</button>
                <div class="text-center text-danger"><?php echo $error ?></div>
            </form>
        </div>
    </div>
</div>