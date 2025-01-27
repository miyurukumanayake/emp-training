<?php
$menu = "reports";
$page = "reports";
$jQueryPlugin = "table2csv.min.js";

require_once 'components/pag-guard.php';

require_once '../server/connection.php';

$error = null;

$planId = null;
$type = "users";
$userId = null;

if (isset($_POST['planId'])) {
    $planId = $_POST['planId'];
}

if (isset($_POST['type'])) {
    $type = $_POST['type'];
}

if (isset($_POST['userId'])) {
    $userId = $_POST['userId'];
}

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <form name="actions" method="post" action="" class="h-100">
        <div class="page-card-header d-flex flex-column <?php if ($type === 'progress') echo 'double' ?>">
            <div class="d-flex justify-content-between w-100">
                <h3 class="app-text-primary">Reports</h3>
                <div class="page-card-header-actions align-items-center">
                    <div class="position-relative d-flex gap-2 align-items-center">
                        <label for="selectType" class="text-nowrap">Report Type: </label>
                        <select id="selectType" name="type"
                                class="form-control app-text-primary position-relative bg-transparent"
                                style="z-index: 2; padding-right: 35px;" onchange="document.actions.submit()">
                            <option class="app-text-primary"
                                    value="users" <?php echo $type === 'users' ? 'selected' : '' ?>>User List
                            </option>
                            <option class="app-text-primary"
                                    value="progress" <?php echo $type === 'progress' ? 'selected' : '' ?>>User Progress
                            </option>
                        </select>
                        <i class="fas fa-caret-down position-absolute"
                           style="top: 50%; transform: translateY(-50%); right: 15px; z-index: 1"></i>
                    </div>

                    <?php if ($type !== 'progress') { ?>
                        <button id="saveReport" class="btn btn-primary bts" style="height: 35px">
                            <i class="fa fa-save"></i>
                        </button>
                    <?php } ?>
                </div>
            </div>
            <div class="page-card-header-actions w-100 justify-content-end mt-2">
                <?php if ($type === 'progress') { ?>
                    <div class="position-relative d-flex gap-2 align-items-center">
                        <label for="selectPlan" class="text-nowrap">Plan: </label>
                        <select id="selectPlan" name="planId"
                                class="form-control app-text-primary position-relative bg-transparent"
                                style="z-index: 2; padding-right: 35px;" onchange="document.actions.submit()">
                            <?php
                            require_once "../server/connection.php";

                            $sql = "SELECT id, name FROM `plans`";
                            $result = $conn->query($sql);
                            $i = 0;
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    if ($i === 0 && !$planId) {
                                        $planId = $row['id'];
                                    }
                                    ?>
                                    <option class="app-text-primary"
                                            value="<?php echo $row['id']; ?>" <?php echo $row['id'] === $planId ? 'selected' : '' ?>><?php echo $row['name']; ?></option>
                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                        </select>
                        <i class="fas fa-caret-down position-absolute"
                           style="top: 50%; transform: translateY(-50%); right: 15px; z-index: 1"></i>
                    </div>
                    <div>
                        <div class="position-relative d-flex gap-2 align-items-center justify-content-end">
                            <label for="selectUser" class="text-nowrap">User: </label>
                            <select id="selectUser" name="userId"
                                    class="form-control app-text-primary position-relative bg-transparent w-auto"
                                    style="z-index: 2; padding-right: 35px;" onchange="document.actions.submit()">
                                <option class="app-text-primary"
                                        value="" <?php echo $userId === '' ? '' : 'selected' ?>>All
                                </option>
                                <?php
                                $sql = "SELECT id, firstName, lastName FROM `users` WHERE role = 'employee'";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        ?>
                                        <option class="app-text-primary"
                                                value="<?php echo $row['id']; ?>" <?php echo $row['id'] === $userId ? 'selected' : '' ?>>
                                            <?php echo $row['id'] . " - " . $row['firstName'] . " " . $row['lastName']; ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <i class="fas fa-caret-down position-absolute"
                               style="top: 50%; transform: translateY(-50%); right: 15px; z-index: 1"></i>
                        </div>
                    </div>
                    <button id="saveReport" class="btn btn-primary bts" style="height: 35px">
                        <i class="fa fa-save"></i>
                    </button>
                <?php } ?>
            </div>
        </div>
        <div class="page-card-body  <?php if ($type === 'progress') echo 'double-header' ?>">
            <?php if ($error) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error</strong> <?php echo $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <table id="reportTable" class="table page-table <?php if ($userId) echo 'table-bordered' ?>"
                   data-report="<?php echo $type === 'progress' ? 'user-progress-report' : 'users-report' ?>">
                <?php
                if ($type === 'users') {
                    ?>
                    <thead>
                    <tr>
                        <th class="">First Name</th>
                        <th class="">Last Name</th>
                        <th class="text-center">Email</th>
                        <th class="">Status</th>
                        <th class="text-center">Contact</th>
                        <th class="">Address</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT * FROM users WHERE role = 'employee'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td>
                                    <div><?php echo $row['firstName'] ?></div>
                                </td>
                                <td>
                                    <div><?php echo $row['lastName'] ?></div>
                                </td>
                                <td class="text-center">
                                    <div><?php echo $row['email'] ?></div>
                                </td>
                                <td>
                                    <div><?php echo $row['status'] === 'active' ? 'Active' : 'Inactive' ?></div>
                                </td>
                                <td class="text-center">
                                    <div><?php echo $row['contact'] ?></div>
                                </td>
                                <td>
                                    <div><?php echo $row['address'] ?></div>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                    <?php
                } else {
                    if ($userId) {
                        ?>
                        <thead>
                        <tr>
                            <th class="text-center">Section</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Task</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Response</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT
                                s.name AS sectionName,
                                s.description AS sectionDesc,
                                (SELECT `status` FROM `user-plan-section` WHERE userId = $userId AND planId = $planId AND sectionId = s.id) sectionStatus,
                                (SELECT COUNT(id) FROM tasks WHERE sectionId = s.id) as taskCount,
                                t.name AS taskName,
                                t.description AS taskDesc,
                                (SELECT `status` FROM `user-plan-tasks` WHERE userId = $userId AND planId = $planId AND taskId = t.id) AS taskStatus,
                                (SELECT `response` FROM `user-plan-tasks` WHERE userId = $userId AND planId = $planId AND taskId = t.id) AS response
                            FROM `sections` s,`tasks` t
                            WHERE s.id = t.sectionId";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td>
                                        <div><?php echo $row['sectionName'] ?></div>
                                        <div class="small"><?php echo $row['sectionDesc'] ?></div>
                                    </td>
                                    <td class="text-nowrap text-center" style="width: 100px;">
                                        <?php echo $row['sectionStatus'] === 'complete' ? 'Completed' : ($row['sectionStatus'] === 'in-progress' ? 'In progress' : 'Not started') ?>
                                    </td>
                                    <td>
                                        <div><?php echo $row['taskName'] ?></div>
                                        <div class="small"><?php echo $row['taskDesc'] ?></div>
                                    </td>
                                    <td class="text-nowrap text-center" style="width: 100px;">
                                        <?php echo $row['taskStatus'] === 'complete' ? 'Completed' : ($row['taskStatus'] === 'in-progress' ? 'In progress' : 'Not started') ?>
                                    </td>
                                    <td style="width: 200px;">
                                        <?php echo $row['response'] ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                        <?php
                    } else {
                        ?>
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-center">All Sections</th>
                            <th class="text-center">In progress</th>
                            <th class="text-center">Completed</th>
                            <th class="text-center">All Tasks</th>
                            <th class="text-center">In progress</th>
                            <th class="text-center">Completed</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT
                                CONCAT(u.firstName, ' ', u.lastName) AS `name`,
                                (SELECT COUNT(id) FROM `plan-sections` WHERE planId = $planId) AS allSections,
                                (SELECT COUNT(id) FROM `tasks` WHERE sectionId IN (SELECT sectionId FROM `plan-sections` WHERE planId = $planId)) AS allTasks,
                                (SELECT COUNT(id) FROM `user-plan-section` WHERE userId = u.id AND planId = $planId AND `status` = 'in-progress') AS inProgressSections,
                                (SELECT COUNT(id) FROM `user-plan-section` WHERE userId = u.id AND planId = $planId AND `status` = 'complete') AS completedSections,
                                (SELECT COUNT(id) FROM `user-plan-tasks` WHERE userId = u.id AND `status` = 'in-progress' AND taskId IN (
                                    SELECT taskId FROM tasks WHERE sectionId IN (SELECT sectionId FROM `plan-sections` WHERE planId = $planId)
                                )) AS inProgressTasks,
                                (SELECT COUNT(id) FROM `user-plan-tasks` WHERE userId = u.id AND `status` = 'complete' AND taskId IN (
                                    SELECT taskId FROM tasks WHERE sectionId IN (SELECT sectionId FROM `plan-sections` WHERE planId = $planId)
                                )) AS completedTasks
                            FROM users u WHERE `role` = 'employee'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?php echo $row['name'] ?></td>
                                    <td class="text-center"><?php echo $row['allSections'] ?></td>
                                    <td class="text-center"><?php echo $row['inProgressSections'] ?></td>
                                    <td class="text-center"><?php echo $row['completedSections'] ?></td>
                                    <td class="text-center"><?php echo $row['allTasks'] ?></td>
                                    <td class="text-center"><?php echo $row['inProgressTasks'] ?></td>
                                    <td class="text-center"><?php echo $row['completedTasks'] ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                        <?php
                    }
                }
                ?>
            </table>
        </div>
    </form>
</div>

<?php require_once 'components/pag-bottom.php' ?>

