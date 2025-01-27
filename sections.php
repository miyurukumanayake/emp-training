<?php
$menu = "sections";
$page = "sections";

require_once 'components/pag-guard.php';

$error = null;

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary">Sections</h3>
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

        <?php
        if ($_SESSION['selectedPlan']) {
            $userId = $user['id'];
            $planId = $_SESSION['selectedPlan']['id'];
            $sql = "SELECT
                        sectionId as id,
                        (SELECT `name` FROM sections WHERE id = sectionId) as `name`,
                        (SELECT `status` FROM `user-plan-section` WHERE userId = $userId AND planId = $planId AND sectionId = ps.sectionId) AS `status`,
                        (SELECT COUNT(id) FROM `tasks` WHERE sectionId = ps.sectionId) AS tasks,
                        (SELECT COUNT(id) FROM `user-plan-tasks` WHERE userId = $userId AND taskId IN (
                            (SELECT id FROM `tasks` t WHERE t.sectionId = ps.sectionId)
                        ) AND `status` = 'complete') AS completedTasks
                    FROM `plan-sections` ps WHERE planId = $planId AND sectionId in (
                        SELECT id FROM sections WHERE name LIKE '%$keyword%'
                    ) ORDER BY `time`";
            $result = $conn->query($sql);
            $i = 0;
            while ($row = $result->fetch_assoc()) {
                $i++;
                ?>
                <a class="text-decoration-none" href="section.php?id=<?php echo $row['id'] ?>">
                    <div class="section-card card d-flex flex-row align-items-center justify-content-between p-3 mb-3 cursor-pointer">
                        <div class="d-flex gap-3 align-items-center">
                            <div class="section-number">
                                <div class="fw-bold app-text-primary"><?php echo $i ?></div>
                            </div>
                            <div class="section-title fw-bold app-text-primary"><?php echo $row['name'] ?></div>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <div class="section-status py-1 px-2 rounded-3 <?php echo $row['status'] === 'in-progress' ? 'bg-warning text-white' : ($row['status'] === 'complete' ? 'bg-success text-white' : '') ?>">
                                <?php echo $row['status'] === 'in-progress' ? 'In Progress' : ($row['status'] === 'complete' ? 'Completed' : '') ?>
                            </div>
                            <div class="section-progress">
                                <div class="rounded-3 bg-secondary text-white py-1 px-2 d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-clipboard"></i>
                                    <div><?php echo $row['completedTasks'] ?>/<?php echo $row['tasks'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                <?php
            }
        }
        ?>
    </div>
</div>

<?php require_once 'components/pag-bottom.php' ?>

