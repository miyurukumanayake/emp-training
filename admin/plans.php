<?php
$menu = "plans";
$page = "plans";

require_once 'components/pag-guard.php';

require_once '../server/connection.php';

$error = null;

if (isset($_POST['deletePlan'])) {
    $planId = $_POST['planId'];

    $sql = "DELETE FROM plans WHERE id = $planId";

    if ($conn->query($sql) !== TRUE) {
        $error = "Error deleting plan";
        $errorDetails = $conn->error;
        echo "<script>console.log(\"$errorDetails\")</script>";
    } else {
        header("Location: /admin/plans.php");
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
        <h3 class="app-text-primary">Plans</h3>
        <div class="page-card-header-actions">
            <a class="btn btn-primary" href="save-plan.php">
                <i class="fas fa-plus"></i>
                <span>Add Plan</span>
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
            $sql = "SELECT * FROM plans";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['name']?></td>
                        <td class="actions">
                            <form method="post" action="" class="d-flex gap-2">
                                <input type="hidden" name="planId" value="<?php echo $row['id'] ?>">
                                <input type="hidden" name="status" value="<?php echo $row['status'] === 'active' ? 'inactive' : 'active' ?>">
                                <button name="changeStatus" class="d-none"></button>
                                <button type="button" class="changeStatus btn btn-dark" data-status="<?php echo $row['status'] === 'active' ? 'deactivate' : 'activate' ?>">
                                    <i class="fas <?php echo $row['status'] === 'active' ? 'fa-ban' : 'fa-check' ?>"></i>
                                    <span><?php echo $row['status'] === 'active' ? 'Deactivate' : 'Activate' ?></span>
                                </button>
                                <a class="btn btn-warning" href="save-plan.php?id=<?php echo $row['id']?>">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <button name="deletePlan" class="d-none"></button>
                                <button type="button" class="deletePlan btn btn-danger">
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
                        No plans found
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
    $(".deletePlan").on('click', async function () {
        const res = await confirmModal("Confirm Delete", "Are you sure you want to delete this plan?", "Delete", 'btn-danger');
        if (res) {
            $(this).closest('form').find('button[name="deletePlan"]').click();
        }
    });

    $(".changeStatus").on('click', async function () {
        const status = $(this).data('status');
        const buttonText = status === 'activate' ? 'Activate Plan' : 'Deactivate Plan';
        const res = await confirmModal("Confirm Delete", `Are you sure you want to ${status} this plan?`, buttonText, 'btn-dark');
        if (res) {
            $(this).closest('form').find('button[name="changeStatus"]').click();
        }
    });
</script>

<?php require_once 'components/pag-bottom.php' ?>

