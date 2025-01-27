<?php
session_start();
$menu = "materials";
$page = "materials";

require_once 'components/pag-guard.php';

$error = null;

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary">Reading Materials</h3>
        <div class="page-card-header-actions"></div>
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
            <?php

            require_once 'server/connection.php';
            $planId = $_SESSION['selectedPlan']['id'] ?? null;
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
                            <button data-download data-file="<?php echo $row['file']?>" data-name="<?php echo $row['name']?>.<?php echo $row['ext']?>" class="btn btn-dark">
                                <i class="fas fa-download"></i>
                                <span>Download</span>
                            </button>
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

<?php require_once 'components/pag-bottom.php' ?>

