<?php
$menu = "organization";
$page = "sections";

require_once 'components/pag-guard.php';

require_once '../server/connection.php';

$error = null;
$config = $_SESSION['config'];

$sql = "SELECT * FROM config";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
   $config[$row['key']] = $row['value'];
}

if (isset($_POST['saveOrganization'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $logo = "";

    if ($_FILES["logo"] && $_FILES["logo"]['size']) {
        $target_dir = "../uploads/logo/";
        $target_file = $target_dir . time() . "." . strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
        $uploadOk = 1;
        $logoFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["logo"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "File is not an logo.";
            $uploadOk = 0;
        }

        if ($_FILES["logo"]["size"] > 500000) {
            $error = "File is too large.";
            $uploadOk = 0;
        }

        if ($logoFileType != "jpg" && $logoFileType != "png" && $logoFileType != "jpeg"
            && $logoFileType != "gif") {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            $error = "Logo was not uploaded. " . $error;
        } else {
            if (!move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $error = "There was an error uploading profile picture.";
            } else {
                $logo = str_replace("../", "", $target_file);
            }
        }
    }

    if (!$error) {
        $logoSql = $logo ? ", ('logo', '$logo')" : "";
        $sql = "
            INSERT INTO config (`key`, `value`) VALUES 
                ('name', '$name'),
                ('address', '$address'),
                ('contact', '$contact')
                $logoSql
            ON DUPLICATE KEY UPDATE VALUE = VALUES(VALUE)";
        if ($conn->query($sql) === TRUE) {
            $config['name'] = $name;
            $config['address'] = $address;
            $config['contact'] = $contact;
            if ($logo) {
                $config['logo'] = $logo;
            }
            $_SESSION['config'] = $config;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error = "Error updating organization";
            $errorDetails = $conn->error;
            echo '<script>console.log("' . $errorDetails . '")</script>';
        }
    }
}

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary">Organization</h3>
        <div class="page-card-header-actions"></div>
    </div>
    <div class="page-card-body">
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error</strong> <?php echo $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data" class="mx-1">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input id="name" name="name"
                       value="<?php if ($config) echo htmlspecialchars(stripslashes($config['name'])) ?>"
                       class="form-control">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input id="address" name="address"
                       value="<?php if ($config) echo htmlspecialchars(stripslashes($config['address'])) ?>"
                       class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input id="contact" name="contact"
                       value="<?php if ($config) echo htmlspecialchars(stripslashes($config['contact'])) ?>"
                       class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label">Logo</label>
                <input id="logo" name="logo" type="file" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary" name="saveOrganization">
                <i class="fas fa-save"></i>
                <span>
                    Update Organization
                </span>
            </button>
        </form>
    </div>
</div>

<?php require_once 'components/pag-bottom.php' ?>

