<?php
$menu = "dashboard";
$page = "index";

require_once 'components/pag-guard.php';

$error = null;

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <!--    <div class="page-card-header">-->
    <!--        <h3>Dashboard</h3>-->
    <!--        <div class="page-card-header-actions"></div>-->
    <!--    </div>-->
    <div class="page-card-body">
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error</strong> <?php echo $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <div class="dashboard-card card p-4 dashboard-spacing">
            <h6>Welcome back</h6>
            <h5 class="fw-bold mb-3"><?php echo $user['firstName'] . " " . $user['lastName'] ?></h5>
            <h6 class="fw-bold">
                Plan: <?php if ($_SESSION['selectedPlan']) echo $_SESSION['selectedPlan']['name'] ?></h6>
        </div>

        <div class="d-flex">
            <div class="status-card-wrapper col-6">
                <?php
                $plan = $_SESSION['selectedPlan'];
                $now = new DateTime();
                ?>
                <div class="status-card card p-4 dashboard-spacing">
                    <h6 class="fw-bold app-text-primary">Status</h6>
                    <div class="date"><?php echo $plan['time'] ?> - <?php echo $now->format('M d, Y') ?></div>
                    <?php
                    $userId = $user['id'];
                    $planId = $plan['id'];
                    $sql = "SELECT
                            (SELECT COUNT(id) FROM tasks t WHERE t.sectionId IN (SELECT sectionId FROM `plan-sections` WHERE planId = $planId)) AS allTasks,
                            (SELECT COUNT(t.id) FROM tasks t, `user-plan-tasks` ut WHERE t.id = ut.taskId AND userId = $userId AND `status` = 'complete' AND t.sectionId IN (SELECT sectionId FROM `plan-sections` WHERE planId = $planId)) AS completedTasks,
                            (SELECT COUNT(t.id) FROM tasks t, `user-plan-tasks` ut WHERE t.id = ut.taskId AND userId = $userId AND `status` = 'in-progress' AND t.sectionId IN (SELECT sectionId FROM `plan-sections` WHERE planId = $planId)) AS inProgressTasks";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $data = $result->fetch_assoc();
                        $remainingTasks = $data['allTasks'] - $data['completedTasks'] - $data['inProgressTasks'];
                        $completedPercent = $data['allTasks'] > 0 ? round(($data['completedTasks'] / $data['allTasks']) * 100, 1) : 0;
                        $inProgressPercent = $data['allTasks'] > 0 ? round(($data['inProgressTasks'] / $data['allTasks']) * 100, 1) : 0;
                        $remainingPercent = $data['allTasks'] > 0 ? round(100 - ($completedPercent + $inProgressPercent), 1) : 0;
                        ?>
                        <div class="mt-3 d-flex justify-content-between">
                            <div>
                                <h5 class="fw-bold text-success"><?php echo $completedPercent ?>%</h5>
                                <div class="progress-label text-success">Completed</div>
                            </div>
                            <div>
                                <h5 class="text-end fw-bold text-warning"><?php echo $inProgressPercent ?>%</h5>
                                <div class="progress-label text-warning">In progress</div>
                            </div>
                            <div>
                                <h5 class="text-end fw-bold text-secondary"><?php echo $remainingPercent ?>%</h5>
                                <div class="progress-label text-secondary">Remaining</div>
                            </div>
                        </div>
                        <div class="progress-stacked mt-3">
                            <div class="progress" role="progressbar" style="width: <?php echo $completedPercent ?>%">
                                <div class="progress-bar bg-success"></div>
                            </div>
                            <div class="progress" role="progressbar" style="width: <?php echo $inProgressPercent ?>%">
                                <div class="progress-bar bg-warning"></div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <div class="recent-card-wrapper col-6">
                <div class="recent-card card p-4 dashboard-spacing">
                    <h6 class="fw-bold mb-3 app-text-primary">Recently Completed Sections</h6>
                    <div class="d-flex flex-column flex-grow-1 justify-content-start">
                        <?php
                        $userId = $user['id'];
                        $planId = $_SESSION['selectedPlan']['id'];
                        $sql = "SELECT
                                    s.id as id,
                                    s.name as name,
                                    DATE_FORMAT(completeTime, '%b %d, %Y') as completeTime
                                FROM `user-plan-section` ups, sections s
                                WHERE ups.sectionId = s.id AND userId = $userId AND planId = $planId AND status = 'complete'
                                LIMIT 3";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <a href="section.php?id=<?php echo $row['id'] ?>"
                                   class="text-decoration-none text-black">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <div><?php echo $row['name'] ?></div>
                                        <div class="date">Completed: <?php echo $row['completeTime'] ?></div>
                                    </div>
                                </a>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 ps-1">
            <h6 class="mb-3 fw-bold app-text-primary">Due Tasks</h6>

            <?php $limit = 3;
            require_once "components/due-tasks.php" ?>
        </div>
    </div>
</div>

<?php require_once 'components/pag-bottom.php' ?>

