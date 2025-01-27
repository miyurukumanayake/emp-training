<?php
$menu = "sections";
$page = "sections";

require_once 'components/pag-guard.php';

require_once '../server/connection.php';

$error = null;

if (isset($_POST['deleteSection'])) {
    $sectionId = $_POST['sectionId'];

    $sql = "DELETE FROM sections WHERE id = $sectionId";

    if ($conn->query($sql) !== TRUE) {
        $error = "Error deleting section";
        $errorDetails = $conn->error;
        echo "<script>console.log(\"$errorDetails\")</script>";
    } else {
        header("Location: /admin/sections.php");
        exit();
    }
}

require_once 'components/pag-top.php';
?>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary">Sections</h3>
        <div class="page-card-header-actions">
            <a class="btn btn-primary" href="save-section.php">
                <i class="fas fa-plus"></i>
                <span>Add Section</span>
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
            $sql = "SELECT id, name FROM sections";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['name']?></td>
                        <td class="actions">
                            <a class="btn btn-warning" href="save-section.php?id=<?php echo $row['id']?>">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                            <form method="post" action="">
                                <input type="hidden" name="sectionId" value="<?php echo $row['id'] ?>">
                                <button name="deleteSection" class="d-none"></button>
                                <button type="button" class="deleteSection btn btn-danger">
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
                        No sections found
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
    $(".deleteSection").on('click', async function () {
        const res = await confirmModal("Confirm Delete", "Are you sure you want to delete this section?", "Delete", 'btn-danger');
        if (res) {
            $(this).closest('form').find('button[name="deleteSection"]').click();
        }
    });
</script>

<?php require_once 'components/pag-bottom.php' ?>

