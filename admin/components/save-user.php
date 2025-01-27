<div class="page-card">
    <div class="page-card-header">
        <h3 class="app-text-primary">
            <?php
            if ($role !== 'profile') {
                echo isset($selectedUser)
                    ? "Update " . ($role === 'admin' ? 'Admin User' : 'Employee') . " - " . $selectedUser['firstName'] . " " . $selectedUser['lastName']
                    : "Create " . ($role === 'admin' ? 'Admin User' : 'Employee');

            } else {
                echo "Update Profile";
            }
            ?>
        </h3>

        <div class="page-card-header-actions">
            <?php if ($role !== 'profile') {
                ?>
                <a class="btn btn-secondary" href="/admin<?php echo $role === 'admin' ? '' : '/employees.php' ?>">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
                <?php
            } ?>
        </div>
    </div>
    <div class="page-card-body container">
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error</strong> <?php echo $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <?php if ($success) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Error</strong> <?php echo $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . ($role !== 'profile' ? "?id=$selectedUserId" : "") ?>"
              enctype="multipart/form-data" class="mx-1 <?php echo $role === 'profile' ? 'd-flex' : ''?>">
            <div class="col-6 pe-4">
                <h4 class="mb-3 app-text-primary">Account Info</h4>
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input id="firstName" name="firstName"
                           value="<?php if ($selectedUser) echo htmlspecialchars(stripslashes($selectedUser['firstName'])) ?>"
                           class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input id="lastName" name="lastName"
                           value="<?php if ($selectedUser) echo htmlspecialchars(stripslashes($selectedUser['lastName'])) ?>"
                           class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" name="email"
                           value="<?php if ($selectedUser) echo htmlspecialchars(stripslashes($selectedUser['email'])) ?>"
                           class="form-control" required>
                </div>
                <?php if ($role !== 'profile') {?>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password"
                           placeholder="<?php if ($selectedUser) echo "<unchanged>" ?>"
                           class="form-control" <?php echo $selectedUser ? "" : "required" ?>>
                </div>
                <?php } ?>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input id="address" name="address"
                           value="<?php if ($selectedUser) echo htmlspecialchars(stripslashes($selectedUser['address'])) ?>"
                           class="form-control">
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact</label>
                    <input id="contact" name="contact"
                           value="<?php if ($selectedUser) echo htmlspecialchars(stripslashes($selectedUser['contact'])) ?>"
                           class="form-control">
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Profile Picture</label>
                    <input id="image" name="image" type="file" class="form-control">
                </div>
                <?php if ($role === "employee") { ?>
                    <div class="mb-3">
                        <label for="plans" class="form-label">Plans</label>
                        <select name="plans[]" id="plans" class="form-control" multiple>
                            <?php
                            for ($i = 0; $i < sizeof($plans); $i++) {
                                ?>
                                <option value="<?php echo $plans[$i]['id'] ?>" <?php echo $selectedUser && in_array($plans[$i]['id'], $selectedUser['planIds']) ? 'selected' : '' ?>>
                                    <?php echo $plans[$i]['name'] ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                <?php } ?>
                <button type="submit" class="btn btn-primary" name="saveUser">
                    <i class="fas fa-save"></i>
                    <span>
                    <?php echo $selectedUser ? "Update " : "Create " ?><?php echo $role === 'admin' ? 'Admin User' : ($role === 'employee' ? 'Employee' : "Profile") ?>
                </span>
                </button>
            </div>
            <?php if($role === 'profile') {?>
            <div class="col-6 ps-4">
                <h4 class="mb-3 app-text-primary">Change Password</h4>

                <div class="mb-3">
                    <label for="password" class="form-label">Current Password</label>
                    <input id="password" name="oldPassword" class="form-control" <?php echo $selectedUser ? "" : "required" ?>>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input id="password" name="newPassword" class="form-control" <?php echo $selectedUser ? "" : "required" ?>>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Confirm Password</label>
                    <input id="password" name="confirmPassword" class="form-control" <?php echo $selectedUser ? "" : "required" ?>>
                </div>
                <button type="submit" class="btn btn-primary" name="changePassword">
                    <i class="fas fa-save"></i>
                    Change Password
                </button>
            </div>
            <?php } ?>
        </form>
    </div>
</div>