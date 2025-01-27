<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary"><?php echo $role === 'admin' ? 'Admin Users' : 'Employees' ?></h3>
        <div class="page-card-header-actions">
            <a class="btn btn-primary" href="/admin/save-<?php echo $role === 'admin' ? 'admin' : 'employee' ?>.php">
                <i class="fas fa-plus"></i>
                <span>Add <?php echo $role === 'admin' ? 'Admin User' : 'Employee' ?></span>
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
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th class="actions">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            /** @noinspection PhpIncludeInspection */
            require_once '../server/connection.php';
            $sql = "SELECT * FROM users WHERE role = '$role'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['firstName'] ?></td>
                        <td><?php echo $row['lastName'] ?></td>
                        <td><?php echo $row['email'] ?></td>
                        <td class="actions">
                            <form method="post" action="" class="d-flex gap-2 <?php if ($row['id'] === $_SESSION['user']['id']) echo "invisible" ?>">
                                <input type="hidden" name="userId" value="<?php echo $row['id'] ?>">
                                <input type="hidden" name="status"
                                       value="<?php echo $row['status'] === 'active' ? 'inactive' : 'active' ?>">
                                <button name="changeStatus" class="d-none"></button>
                                <button type="button" class="changeStatus btn btn-dark"
                                        data-status="<?php echo $row['status'] === 'active' ? 'deactivate' : 'activate' ?>">
                                    <i class="fas <?php echo $row['status'] === 'active' ? 'fa-ban' : 'fa-check' ?>"></i>
                                    <span><?php echo $row['status'] === 'active' ? 'Deactivate' : 'Activate' ?></span>
                                </button>
                                <a class="btn btn-warning"
                                   href="/admin/save-<?php echo $role ?>.php?id=<?php echo $row['id'] ?>">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <button name="deleteUser" class="d-none"></button>
                                <button type="button" class="deleteUser btn btn-danger">
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
                        No <?php echo $role === 'admin' ? 'admins' : 'employees' ?> found
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
    $(".deleteUser").on('click', async function () {
        const res = await confirmModal("Confirm Delete", "Are you sure you want to delete this user?", "Delete", 'btn-danger');
        if (res) {
            $(this).closest('form').find('button[name="deleteUser"]').click();
        }
    });

    $(".changeStatus").on('click', async function () {
        const status = $(this).data('status');
        const buttonText = status === 'activate' ? 'Activate User' : 'Deactivate User';
        const res = await confirmModal("Confirm Delete", `Are you sure you want to ${status} this user?`, buttonText, 'btn-dark');
        if (res) {
            $(this).closest('form').find('button[name="changeStatus"]').click();
        }
    });
</script>