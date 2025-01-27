<?php
$menu = "tasks";
$page = "tasks";

require_once 'components/pag-guard.php';

$error = null;

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary">Due Tasks</h3>
        <div class="page-card-header-actions"></div>
    </div>
    <div class="page-card-body">
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error</strong> <?php echo $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <?php require_once "components/search.php" ?>

        <?php require_once "components/due-tasks.php" ?>
    </div>
</div>

<?php require_once 'components/pag-bottom.php' ?>

