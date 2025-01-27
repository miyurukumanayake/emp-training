<?php
/** @noinspection PhpIncludeInspection */
if(file_exists('../server/connection.php')) require_once '../server/connection.php';

$selectedUserId = null;
$selectedUser = null;
$error = null;
$success = null;

$plans = [];
$planIds = [];

if ($role === 'profile') {
    $selectedUser = $_SESSION['user'];
    $selectedUserId = $selectedUser['id'];
} else {
    if (isset($_GET['id']) && $_GET['id']) {
        $selectedUserId = $_GET['id'];
        $sql = "SELECT * FROM users WHERE id = $selectedUserId";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $selectedUser = $result->fetch_assoc();
            $selectedUser['planIds'] = [];
        }
    }
}

if ($role === 'employee') {
    $sql = "SELECT * FROM plans";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $plans[] = $row;
        }
    }

    if ($selectedUser) {
        $sql = "SELECT * FROM `user-plans` WHERE userId = $selectedUserId";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $selectedUser['planIds'][] = $row['planId'];
            }
        }
    }
}

if (isset($_POST['changePassword'])) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword !== $confirmPassword) {
        $error = "New password amd confirm does not match";
    } else {
        $sql = "SELECT password FROM users WHERE email = '" . $selectedUser['email'] . "'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['password'] === md5($oldPassword)) {
                $hash = md5($newPassword);
                $sql = "UPDATE users SET password = '$hash' WHERE email = '" . $selectedUser['email'] . "'";
                if ($conn->query($sql) === TRUE) {
                    $success = "Password changed successfully";
                } else {
                    $error = "Something went wrong";
                }
            } else {
                $error = "Invalid old password";
            }
        } else {
            $error = "Something went wrong";
        }
    }
}

if (isset($_POST['saveUser'])) {
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'] ? md5($_POST['password']) : null;
    $address = $conn->real_escape_string($_POST['address']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $planIds = isset($_POST['plans']) && $_POST['plans'] ? $_POST['plans'] : [];
    $image = "";

    if ($_FILES["image"] && $_FILES["image"]['size']) {
        $target_dir = "../uploads/profile/";
        $target_file = $target_dir . time() . "." . strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "File is not an image.";
            $uploadOk = 0;
        }

        if ($_FILES["image"]["size"] > 500000) { // 500KB
            $error = "File is too large.";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            $error = "Profile picture was not uploaded. " . $error;
        } else {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $error = "There was an error uploading profile picture.";
            } else {
                $image = str_replace("../", "", $target_file);
            }
        }
    }

    if (!$error) {
        if ($selectedUserId) {
            $imageSql = $image ? ", image = '$image'" : "";
            if ($password == "") {
                $sql = "
                UPDATE users
                SET firstName = '$firstName', lastName = '$lastName', email = '$email',  address = '$address', contact = '$contact' $imageSql
                WHERE id = $selectedUserId";
            } else {
                $sql = "
                UPDATE users
                SET firstName = '$firstName', lastName = '$lastName', email = '$email', password = '$password', address = '$address', contact = '$contact' $imageSql
                WHERE id = $selectedUserId";
            }
        } else {
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $error = "User with same email already exists";
            } else {
                $sql = "INSERT INTO users (firstName, lastName, email, password, role, address, contact, image)
                VALUES ('$firstName', '$lastName', '$email', '$password', '$role', '$address', '$contact', '$image')";
            }
        }

        if (!$error) {
            if ($conn->query($sql) === TRUE) {
                if (!$selectedUserId) {
                    $selectedUserId = $conn->insert_id;
                }

                if ($planIds) {
                    $sql = "DELETE FROM `user-plans` WHERE userId = $selectedUserId";
                    if ($conn->query($sql) === TRUE) {
                        $values = "";
                        for ($j = 0; $j < sizeof($planIds); $j++) {
                            $values = $values . "($selectedUserId, " . $planIds[$j] . ")";
                            if ($j !== sizeof($planIds) - 1) {
                                $values = $values . ", ";
                            }
                        }
                        $sql = "INSERT INTO `user-plans` (userId, planId) VALUES $values";
                        if ($conn->query($sql) !== TRUE) {
                            $error = "Error saving employee";
                            $errorDetails = $conn->error;
                            echo "<script>console.log(\"$errorDetails\")</script>";
                        }
                    } else {
                        $error = "Error saving employee";
                        $errorDetails = $conn->error;
                        echo "<script>console.log(\"$errorDetails\")</script>";
                    }
                }

                if (!$error) {
                    if ($role !== 'profile' && !$selectedUserId) {
                        header("Location: /admin" . $role === 'admin' ? '' : '/employees.php');
                        exit();
                    }

                    $selectedUser = $conn->query("SELECT * FROM users WHERE id = $selectedUserId")->fetch_assoc();
                    if ($role === 'profile') {
                        $_SESSION['user'] = $selectedUser;
                    }

                    header("Location: " . $_SERVER['PHP_SELF'] . ($role !== 'profile' ? "?id=$selectedUserId" : ""));
                    exit();
                }
            } else {
                $error = "Error saving " . ($role === 'admin' ? 'admin user' : 'employee');
                $errorDetails = $conn->error;
                echo "<script>console.log(\"$errorDetails\")</script>";
            }
        }
    }
}