<?php
$menu = "sections";
$page = "save-section";

require_once 'components/pag-guard.php';

require_once '../server/connection.php';

$sectionId = null;
$section = null;
$error = null;

if (isset($_GET['id']) && $_GET['id']) {
    $sectionId = $_GET['id'];
}

if (isset($_POST['saveSection'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $video = $conn->real_escape_string($_POST['video']);
    $description = $conn->real_escape_string($_POST['description']);

    if ($sectionId) {
        $sql = "UPDATE sections SET name = '$name', video = '$video', description = '$description' WHERE id = $sectionId";
    } else {
        $sql = "SELECT * FROM sections WHERE name = '$name'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $error = "Section with same name already exists";
        } else {
            $sql = "INSERT INTO sections (name, video, description) VALUES ('$name', '$video', '$description')";
        }
    }

    if (!$error) {
        if ($conn->query($sql) === TRUE) {
            if (!$sectionId) {
                $sectionId = $conn->insert_id;
                $section['id'] = $sectionId;
            }
            $section['name'] = $name;
            $section['video'] = $video;
            $section['description'] = $description;
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=$sectionId");
            exit();
        } else {
            $error = "Error saving section";
            $errorDetails = $conn->error;
            echo "<script>console.log(\"$errorDetails\")</script>";
        }
    }
}

if ($sectionId) {
    $sql = "SELECT * FROM sections WHERE id = $sectionId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $section = $result->fetch_assoc();
    }

    if (isset($_POST['addTask'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $description = $conn->real_escape_string($_POST['description']);
        $duration = $conn->real_escape_string($_POST['duration']);

        $sql = "INSERT INTO tasks (name, description, duration, sectionId) VALUES ('$name', '$description', $duration, $sectionId)";
        if ($conn->query($sql) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=$sectionId");
            exit();
        } else {
            $error = "Error adding task";
            $errorDetails = $conn->error;
            echo "<script>console.log(\"$errorDetails\")</script>";
        }
    }

    if (isset($_POST['deleteTask'])) {
        $taskId = $_POST['taskId'];

        $sql = "DELETE FROM tasks WHERE id = $taskId";
        if ($conn->query($sql) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=$sectionId");
            exit();
        } else {
            $error = "Error deleting task";
            $errorDetails = $conn->error;
            echo "<script>console.log(\"$errorDetails\")</script>";
        }
    }
}

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary"><?php echo isset($section) ? "Edit Section - " . $section['name'] : "Create Section" ?></h3>
        <div class="page-card-header-actions">
            <a class="btn btn-secondary" href="sections.php">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>
    </div>
    <div class="page-card-body container">
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error</strong> <?php echo $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <form method="post" action="<?php echo "?id=$sectionId" ?>" class="mx-1">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input id="name" name="name"
                       value="<?php if ($section) echo htmlspecialchars(stripslashes($section['name'])) ?>"
                       class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="video" class="form-label">Video URL</label>
                <input id="video" name="video" placeholder="https://www.youtube.com/watch?v=XXXXX"
                       value="<?php if ($section) echo htmlspecialchars(stripslashes($section['video'])) ?>"
                       class="form-control">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"
                          style="resize: none"><?php if ($section) echo htmlspecialchars(stripslashes($section['description'])) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="saveSection">
                <i class="fas fa-save"></i>
                <span><?php echo $section ? "Update" : "Create"?> Section</span>
            </button>
        </form>

        <?php if (isset($section)) { ?>
        <div class="hr my-4 mx-auto"></div>

        <?php if ($error) { ?>
        <div class="text-center text-danger my-3"><?php echo $error ?></div>
        <?php } ?>

        <div class="row position-sticky top-0 h-100">
            <div class="col-6 h-100">
                <div class="task-list h-100 overflow-y-auto">
                    <?php

                    $sql = "SELECT id, name, description FROM tasks WHERE sectionId = $sectionId";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <div class="section-task">
                                <div class="task-heading d-flex justify-content-between">
                                    <h6 class="fw-bold">
                                        <?php echo $row['name'] ?>
                                    </h6>
                                    <form method="post" action="<?php echo "?id=$sectionId" ?>">
                                        <input type="hidden" name="taskId" value="<?php echo $row['id'] ?>">
                                        <button type="submit" name="deleteTask" class="btn btn-sm btn-danger">
                                            <i class="fa fa-close small"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="hr w-100 mb-2"></div>
                                <div class="task-body">
                                    <?php echo $row['description'] ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="col-6 h-100">
                <div class="section-list h-100 px-1 overflow-y-auto">
                    <form method="post" action="<?php echo "?id=$sectionId" ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">Task Name</label>
                            <input id="name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="6"
                                      style="resize: none"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="duration" class="form-label">Task Duration (Days)</label>
                            <input id="duration" name="duration" type="number" value="1" min="1" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary ms-auto" name="addTask">
                            <i class="fas fa-plus"></i>
                            <span>Add Task</span>
                        </button>
                        <div class="text-center text-danger"><?php echo $error ?></div>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

    <style>
    </style>

<?php require_once 'components/pag-bottom.php' ?>

