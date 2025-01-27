<?php
$menu = "sections";
$page = "section";

require_once 'components/pag-guard.php';

require_once 'server/connection.php';

$error = null;
$sectionId = null;
$section = null;
$videoId = null;
$tasks = [];
$userTasks = [];

if (isset($_GET['id']) && $_GET['id']) {
    $userId = $user['id'];
    $sectionId = $_GET['id'];
    $planId = $_SESSION['selectedPlan'] ? $_SESSION['selectedPlan']['id'] : null;

    if (!$planId) {
        header('Location: sections.php');
        exit();
    }

    if (isset($_POST["startTask"])) {
        $taskId = $conn->real_escape_string($_POST['taskId']);
        $sql = "INSERT INTO `user-plan-tasks` (userId, planId, taskId) VALUES ($userId, $planId, $taskId)";
        if ($conn->query($sql) === TRUE) {
            $sql = "INSERT IGNORE INTO `user-plan-section` (userId, planId, sectionId) VALUES ($userId, $planId, $sectionId)";
            if ($conn->query($sql) === TRUE) {
                echo "<script>
                          document.addEventListener('DOMContentLoaded', () => {
                              document.getElementById('task$taskId')?.scrollIntoView({ behavior: 'smooth' });
                          });
                      </script>";
            } else {
                $error = "Error starting task";
                $errorDetails = $conn->error;
                echo "<script>console.log(\"$errorDetails\")</script>";
            }
        } else {
            $error = "Error starting task";
            $errorDetails = $conn->error;
            if (strpos($errorDetails, "Duplicate") !== false) {
                $error = "Task is already started";
            }
            echo "<script>console.log(\"$errorDetails\")</script>";
        }
    }

    if (isset($_POST["completeTask"])) {
        $taskId = $conn->real_escape_string($_POST['taskId']);
        $response = $conn->real_escape_string($_POST['response']);
        $sql = "UPDATE `user-plan-tasks` SET response = '$response', status = 'complete', completeTime = now() WHERE userId = $userId AND taskId = $taskId";
        if ($conn->query($sql) === TRUE) {
            $sql = "SELECT
                COUNT(id) as taskCount,
                (SELECT COUNT(id) FROM `user-plan-tasks` WHERE userId = $userId AND taskId IN (SELECT id FROM tasks WHERE sectionId = $sectionId) AND status = 'complete') as userTaskCount
            FROM tasks WHERE sectionId = $sectionId";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                if ($data['taskCount'] === $data['userTaskCount']) {
                    $sql = "UPDATE `user-plan-section` SET `status` = 'complete', completeTime = NOW() WHERE userId = $userId AND planId = $planId AND sectionId = $sectionId";
                    if ($conn->query($sql) !== TRUE) {
                        $error = "Error completing task";
                        $errorDetails = $conn->error;
                        echo "<script>console.log(\"$errorDetails\")</script>";
                        return;
                    }
                }
            }

            echo "<script>
                      document.addEventListener('DOMContentLoaded', () => {
                          document.getElementById('task$taskId')?.scrollIntoView({ behavior: 'smooth' });
                      });
                  </script>";
        } else {
            $error = "Error completing task";
            $errorDetails = $conn->error;
            echo "<script>console.log(\"$errorDetails\")</script>";
        }
    } elseif (isset($_POST["editResponse"])) {
        $taskId = $conn->real_escape_string($_POST['taskId']);
        echo "<script>
                 document.addEventListener('DOMContentLoaded', () => {
                     document.getElementById('task$taskId')?.scrollIntoView({ behavior: 'smooth' });
                 });
             </script>";
    }

    $sql = "SELECT * FROM sections WHERE id IN (SELECT sectionId FROM `plan-sections` WHERE planId = $planId) AND id = $sectionId";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $section = $result->fetch_assoc();

        if ($section['video']) {
            $videoId = explode("?v=", $section['video'])[1];
        }

        $sql = "SELECT * FROM `user-plan-tasks` WHERE userId = " . $user['id'];
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $userTasks[] = $row;
            }
        }
    } else {
        header('Location: sections.php');
        exit();
    }
} else {
    header('Location: sections.php');
    exit();
}

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary">Section - <?php echo $section['name'] ?></h3>
        <div class="page-card-header-actions">
            <a class="btn btn-secondary" href="sections.php">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
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

        <div class="d-flex mb-3">
            <?php
            if ($videoId) {
                ?>
                <div class="col mb-2 me-4">
                    <iframe width="560" height="315"
                            src="https://www.youtube.com/embed/<?php echo $videoId ?>?autoplay=0"></iframe>
                </div>
                <?php
            }
            ?>
            <div class="col">
                <h6 class="fw-bold fs-5 app-text-primary">Overview</h6>
                <p>
                    <?php echo $section['description'] ?>
                </p>
            </div>
        </div>
        <?php
        $sql = "SELECT * FROM tasks WHERE sectionId = $sectionId";
        $result = $conn->query($sql);
        $userTaskTasks = array_map(function ($userTask) {
            return $userTask['taskId'];
        }, $userTasks);

        if ($result->num_rows > 0) {
            ?>
            <div>
                <h5 class="fw-bold mb-3 app-text-primary">Tasks</h5>

                <div class="col-12 col-xxl-10">
                    <?php
                    $i = 0;
                    while ($row = $result->fetch_assoc()) {
                        $userTask = null;
                        $i++;
                        foreach ($userTasks as $ut) {
                            if ($ut['taskId'] === $row['id']) {
                                $userTask = $ut;
                                break;
                            }
                        }
                        ?>
                        <div id="task<?php echo $row['id'] ?>"
                             class="task-card card p-3 mb-3 d-flex flex-row gap-3">
                            <div style="margin-top: 2px">
                                <i class="fa-solid <?php echo $userTask ? $userTask['status'] === 'in-progress' ? 'fa-circle-dot text-warning' : 'fa-circle-check text-success' : 'fa-circle-minus text-secondary' ?>"></i>
                            </div>
                            <div class="">
                                <form method="post" action="<?php echo "?id=$sectionId" ?>">
                                    <input type="hidden" name="taskId" value="<?php echo $row['id'] ?>">
                                    <?php if ($userTask) { ?>
                                        <div class="task-name fw-bold app-text-primary"><?php echo $row['name'] ?></div>
                                        <div class="task-desc"><?php echo $row['description'] ?></div>
                                    <?php } else { ?>
                                        <div class="task-name fw-bold app-text-primary d-flex align-items-center gap-2">
                                            <i class="fa fa-lock"></i>
                                            <div>Task <?php echo $i ?></div>
                                        </div>
                                    <?php } ?>
                                    <?php
                                    if ($userTask) {
                                        try {
                                            $date = new DateTime($userTask['time']);
                                            $date->add(new DateInterval('P' . $row['duration'] . 'D'));
                                            $isOverDue = $date < new DateTime();
                                            if ($userTask['status'] === 'in-progress' || isset($_POST['editResponse'])) {
                                                ?>
                                                <div class="task-date mb-2 <?php echo $isOverDue ? 'text-danger' : ''?>">
                                                    <?php if ($isOverDue) { ?>
                                                        <span class="task-date bg-danger text-white rounded-3 me-1" style="padding: 1px 5px">Overdue</span>
                                                    <?php } ?>
                                                    Due date: <?php echo $date->format('M d, Y') ?></div>
                                                <div class="mb-3">
                                                    <!--suppress HtmlFormInputWithoutLabel -->
                                                    <textarea id="response" name="response"
                                                              class="form-control resize-none" rows="3"
                                                              required><?php echo htmlspecialchars(stripslashes($userTask['response'])) ?></textarea>
                                                </div>
                                                <button name="completeTask" class="btn btn-primary btn-sm">
                                                    <?php echo isset($_POST['editResponse']) ? 'Change Response' : 'Complete' ?>
                                                </button>
                                                <?php
                                            } else {
                                                ?>
                                                <hr>
                                                <div class="task-desc mb-2">
                                                    <div class=" fw-bold">Response:</div>
                                                    <div><?php echo $userTask['response'] ?></div>
                                                </div>
                                                <button name="editResponse" class="btn btn-primary btn-sm">Edit
                                                    Response
                                                </button>
                                                <?php
                                            }
                                        } catch (Exception $e) {
                                        }
                                    } else {
                                        ?>
                                        <div class="task-date mb-2">Duration: <?php echo $row['duration'] ?> day(s)</div>
                                        <button name="startTask" class="btn btn-info text-white btn-sm">Start</button>
                                        <?php
                                    }
                                    ?>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php require_once 'components/pag-bottom.php' ?>

