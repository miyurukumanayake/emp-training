<?php
session_start();
$menu = "materials";
$page = "save-material";

require_once 'components/pag-guard.php';

require_once '../server/connection.php';

$materialId = null;
$material = null;
$error = null;
$success = null;

$planId = $_SESSION['selectedPlan']['id'];

if (isset($_GET['id']) && $_GET['id']) {
    $materialId = $_GET['id'];
    $sql = "SELECT * FROM `reading-materials` WHERE id = $materialId";
    $result = $conn->query($sql);

    var_dump($sql);

    if ($result->num_rows > 0) {
        $material = $result->fetch_assoc();
    }
}

if (isset($_POST['saveMaterial'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $file = "";
    $ext = "";

    if ($_FILES["file"] && $_FILES["file"]['size']) {
        $target_dir = "../uploads/materials/";
        $ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        $target_file = $target_dir . time() . "." . strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $uploadOk = 1;

        if ($_FILES["file"]["size"] > 20971520) { // 20MB
            $error = "File is too large.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            $error = "File was not uploaded. " . $error;
        } else {
            if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $error = "There was an error uploading file.";
            } else {
                $file = str_replace("../", "", $target_file);
            }
        }
    }

    if (!$error) {
        if ($materialId) {
            $fileSql = $file ? ", file = '$file', ext = '$ext'" : "";
            $sql = "UPDATE `reading-materials` SET name = '$name' $fileSql WHERE id = $materialId";
        } else {
            $sql = "INSERT INTO `reading-materials` (name, file, ext, planId) VALUES ('$name', '$file', '$ext',  $planId)";
        }

        if ($conn->query($sql) === TRUE) {
            header("Location: /admin/materials.php");
            exit();
        } else {
            $error = "Error saving material";
            $errorDetails = $conn->error;
            echo "<script>console.log(\"$errorDetails\")</script>";
        }
    }
}

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary"><?php echo isset($material) ? "Edit Material - " . $material['name'] : "Add Material" ?></h3>
        <div class="page-card-header-actions">
            <a class="btn btn-secondary" href="materials.php">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>
    </div>
    <div class="page-card-body container">
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error: </strong> <?php echo $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <form method="post" action="<?php echo "?id=$materialId" ?>" class="mx-1" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input id="name" name="name"
                       value="<?php if ($material) echo htmlspecialchars(stripslashes($material['name'])) ?>"
                       class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="file" class="form-label">File</label>
                <input id="file" name="file" type="file" class="form-control" <?php echo $materialId ? '' : 'required'?>>
            </div>
            <button type="submit" class="btn btn-primary" name="saveMaterial">
                <i class="fas fa-save"></i>
                <span><?php echo $material ? "Update" : "Create" ?> Material</span>
            </button>
            <div class="text-center text-danger"><?php echo $error ?></div>
        </form>
    </div>
</div>

<?php require_once 'components/pag-bottom.php' ?>

