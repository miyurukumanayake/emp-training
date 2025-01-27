<div class="tasks">
    <?php
    if ($_SESSION['selectedPlan']) {
        $planId = $_SESSION['selectedPlan']['id'];
        $userId = $user['id'];
        $limitSql = "";

        /** @noinspection PhpConditionAlreadyCheckedInspection */
        if (isset($limit) && is_int($limit) && $limit > 0) {
            $limitSql = "LIMIT $limit";
        }

        if (!isset($keyword)) {
            $keyword = "";
        }

        $sql = "SELECT
                    taskId,
                    duration,
                    ut.id AS userTasktId,
                    t.sectionId as sectionId,
                    t.name as name,
                    t.description as `description`,
                    ut.time as `time`,
                    s.name as sectionName,
                    DATE_FORMAT(DATE_ADD(ut.time, INTERVAL t.duration DAY), '%b %d, %Y') AS dueDate,
                    DATE_ADD(ut.time, INTERVAL t.duration DAY) < CURDATE() AS overDue
                FROM tasks t, `user-plan-tasks` ut, sections s, `plan-sections` ps
                WHERE
                    ut.userId = $userId
                    AND ut.taskId = t.id
                    AND ut.`status` = 'in-progress'
                    AND s.id = t.sectionId
                    AND s.id IN (SELECT sectionId FROM `plan-sections` WHERE planId = $planId)
                    AND ps.planId = $planId
                    AND ps.sectionId = t.sectionId
                    AND (t.name LIKE '%$keyword%' OR t.description LIKE '%$keyword%')
                ORDER BY dueDate $limitSql";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            ?>
            <a href="/section.php?id=<?php echo $row['sectionId'] ?>" class="text-decoration-none">
                <div id="task<?php echo $row['taskId'] ?>"
                     class="task-card card p-3 mb-3 cursor-pointer d-flex flex-row gap-3  col-12 col-xxl-10">
                    <div class="">
                        <div class="app-text-primary">
                            <span class="task-name fw-bold"><?php echo $row['sectionName'] ?></span>
                            <span> - </span>
                            <span class="task-name fw-bold"><?php echo $row['name'] ?></span>
                        </div>
                        <div class="task-desc"><?php echo $row['description'] ?></div>
                        <div class="task-date <?php echo $row['overDue'] ? 'text-danger' : '' ?>">
                            <?php if ($row['overDue']) { ?>
                                <span class="task-date bg-danger text-white rounded-3 me-1" style="padding: 1px 5px">Overdue</span>
                            <?php } ?>
                            Due date: <?php echo $row['dueDate'] ?>
                        </div>
                    </div>
                </div>
            </a>
            <?php
        }
    }
    ?>
</div>