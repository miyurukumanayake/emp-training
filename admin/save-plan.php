<?php
$menu = "plans";
$page = "save-plan";

require_once 'components/pag-guard.php';

require_once '../server/connection.php';

$planId = null;
$plan = null;
$error = null;
$success = null;

if (isset($_GET['id']) && $_GET['id']) {
    $planId = $_GET['id'];
}

if (isset($_POST['savePlan'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    if ($planId) {
        $sql = "UPDATE plans SET name = '$name', description = '$description' WHERE id = $planId";
    } else {
        $sql = "SELECT * FROM plans WHERE name = '$name'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $error = "Plan with same name already exists";
        } else {
            $sql = "INSERT INTO plans (name, description) VALUES ('$name', '$description')";
        }
    }

    if (!$error) {
        if ($conn->query($sql) === TRUE) {
            if (!$planId) {
                $planId = $conn->insert_id;
                $plan['id'] = $planId;
            }
            $plan['name'] = $name;
            $plan['description'] = $description;
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=$planId");
            exit();
        } else {
            $error = $conn->error;
        }
    }
}

if ($planId) {
    $sql = "SELECT * FROM plans WHERE id = $planId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $plan = $result->fetch_assoc();
    }

    if (isset($_POST['addSection'])) {
        $sectionId = $_POST['sectionId'];

        $sql = "INSERT INTO `plan-sections` (planId, sectionId) VALUES ($planId, $sectionId)";
        if ($conn->query($sql) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=$planId");
            exit();
        } else {
            $error = "Error adding section";
            $errorDetails = $conn->error;
            echo "<script>console.log(\"$errorDetails\")</script>";
        }
    }

    if (isset($_POST['deleteSection'])) {
        $planSectionId = $_POST['planSectionId'];

        $sql = "DELETE FROM `plan-sections` WHERE id = $planSectionId";
        if ($conn->query($sql) !== TRUE) {
            $error = "Error deleting section";
            $errorDetails = $conn->error;
            echo "<script>console.log(\"$errorDetails\")</script>";
        }
    }
}

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary"><?php echo isset($plan) ? "Edit Plan - " . $plan['name'] : "Create Plan" ?></h3>
        <div class="page-card-header-actions">
            <a class="btn btn-secondary" href="plans.php">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
<!--            <form method="post" action="--><?php //echo "?id=$planId" ?><!--">-->
<!--                <button name="assignAll" class="btn btn-primary">-->
<!--                    <span>Assign to all</span>-->
<!--                </button>-->
<!--            </form>-->
        </div>
    </div>
    <div class="page-card-body container">
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error: </strong> <?php echo $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <form method="post" action="<?php echo "?id=$planId" ?>" class="mx-1">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input id="name" name="name"
                       value="<?php if ($plan) echo htmlspecialchars(stripslashes($plan['name'])) ?>"
                       class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"
                          style="resize: none"><?php if ($plan) echo htmlspecialchars(stripslashes($plan['description'])) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="savePlan">
                <i class="fas fa-save"></i>
                <span><?php echo $plan ? "Update" : "Create"?> Plan</span>
            </button>
        </form>

        <?php if (isset($plan)) { ?>
            <div class="hr my-4 mx-auto"></div>

            <div class="row position-sticky top-0 h-100">
                <div class="col-6 h-100">
                    <div class="pt-3 mb-3 d-flex flex-column align-items-center gap-4 h-100 overflow-y-auto">
                        <?php
                        $sql = "SELECT `plan-sections`.id as id, sections.name FROM `plan-sections`
                                JOIN sections ON sectionId = sections.id WHERE planId = $planId
                                ORDER BY `plan-sections`.time";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $count = 0;

                            while ($row = $result->fetch_assoc()) {
                                $count = $count + 1;
                                ?>
                                <div class="plan-section">
                                    <?php echo $row['name'] ?>
                                    <form method="post" action="<?php echo "?id=$planId" ?>">
                                        <input type="hidden" name="planSectionId" value="<?php echo $row['id'] ?>">
                                        <button type="submit" name="deleteSection" class="btn btn-sm btn-danger">
                                            <i class="fa fa-close small"></i>
                                        </button>
                                    </form>
                                    <?php if ($result->num_rows !== $count) { ?>
                                        <i class="plan-section-arrow fa fa-arrow-down small"></i>
                                    <?php } ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="col-6 h-100">
                    <div class="section-list h-100 overflow-y-auto">
                        <?php

                        $sql = "SELECT id, name FROM sections";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <form method="post" action="<?php echo "?id=$planId" ?>">
                                    <input type="hidden" name="sectionId" value="<?php echo $row['id'] ?>">
                                    <button type="submit" name="addSection" class="btn btn-secondary w-100">
                                        <i class="fas fa-plus"></i>
                                        <?php echo $row['name'] ?>
                                    </button>
                                </form>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php require_once 'components/pag-bottom.php' ?>

