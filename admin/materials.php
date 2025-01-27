<?php
$menu = "materials";
$page = "materials";

require_once 'components/pag-guard.php';

require_once '../server/connection.php';

$error = null;

if (isset($_POST['deleteMaterial'])) {
    $planId = $_SESSION['selectedPlan']['id'] ?? null;
    $materialId = $_POST['materialId'];

    $sql = "DELETE FROM `reading-materials` WHERE planId = $planId AND id = $materialId";

    if ($conn->query($sql) !== TRUE) {
        $error = "Error deleting material";
        $errorDetails = $conn->error;
        echo "<script>console.log(\"$errorDetails\")</script>";
    } else {
        header("Location: /admin/materials.php");
        exit();
    }
}

if (isset($_POST['changeStatus'])) {
    $planId = $_POST['planId'];
    $status = $_POST['status'];
    $sql = "UPDATE plans SET status = '$status' WHERE id = $planId";

    if ($conn->query($sql) !== TRUE) {
        $error = "Error changing user status";
        $errorDetails = $conn->error;
        echo "<script>console.log(\"$errorDetails\")</script>";
    } else {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary">Reading Materials</h3>
        <div class="page-card-header-actions">
            <div class="position-relative">
                <!--suppress HtmlFormInputWithoutLabel -->
                <select id="selectPlan" class="form-control app-text-primary position-relative bg-transparent" style="z-index: 2; padding-right: 35px;">
                    <?php
                    require_once "../server/connection.php";

                    $plans = [];
                    $planId = $_SESSION['selectedPlan']['id'] ?? null;

                    $sql = "SELECT id, name FROM `plans`";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $plans[] = $row;
                            ?>
                            <option class="app-text-primary" value="<?php echo $row['id']; ?>" <?php echo $row['id'] === $planId ? 'selected' : '' ?>><?php echo $row['name']; ?></option>
                            <?php
                        }
                        if (!$planId) {
                            $_SESSION['selectedPlan'] = $plans[0];
                            $planId = $plans[0]['id'];
                        }
                    }
                    ?>
                </select>
                <i class="fas fa-caret-down position-absolute" style="top: 50%; transform: translateY(-50%); right: 15px; z-index: 1"></i>
            </div>
            <a class="btn btn-primary" href="save-material.php">
                <i class="fas fa-plus"></i>
                <span>Add Material</span>
            </a>
        </div>
    </div>
    <div class="page-card-body">
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error</strong> <?php echo $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <table class="table page-table">
            <thead>
            <tr>
                <th>Name</th>
                <th class="actions">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php require_once '../server/connection.php';
            $sql = "SELECT * FROM `reading-materials` WHERE planId = $planId";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                            <i class="fa fa-file me-1 app-text-primary"></i>
                            <?php echo $row['name']?>.<?php echo $row['ext']?>
                        </td>
                        <td class="actions">
                            <form method="post" action="" class="d-flex gap-2">
                                <input type="hidden" name="materialId" value="<?php echo $row['id'] ?>">
                                <button data-download data-file="<?php echo $row['file']?>" data-name="<?php echo $row['name']?>.<?php echo $row['ext']?>" class="btn btn-dark">
                                    <i class="fas fa-download"></i>
                                    <span>Download</span>
                                </button>
                                <a class="btn btn-warning" href="save-material.php?id=<?php echo $row['id']?>">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <button name="deleteMaterial" class="d-none"></button>
                                <button type="button" class="deleteMaterial btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="10" class="text-center py-3">
                        No reading materials found
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(".deleteMaterial").on('click', async function () {
        const res = await confirmModal("Confirm Delete", "Are you sure you want to delete this reading material?", "Delete", 'btn-danger');
        if (res) {
            $(this).closest('form').find('button[name="deleteMaterial"]').click();
        }
    });
</script>

<?php require_once 'components/pag-bottom.php' ?>

