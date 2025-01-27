<div id="header">
    <div class="header-content">
        <div class="header-sections h-100 px-4 d-flex align-items-center gap-3">
            <div class="header-logo">
                <a href="/index.php">
                    <?php if ($config && $config['logo']) { ?>
                        <img src="/<?php echo $config['logo'] ?>" alt="Logo">
                    <?php } ?>
                </a>
                <h4 class="m-0 fw-bold app-text-primary">
                    <?php echo $config && $config['name'] ? $config['name'] : '' ?>
                </h4>
            </div>
            <div class="header-title gap-3 app-text-primary">
                <div class="position-relative d-flex align-items-center gap-3">
                    <label for="selectPlan" class="form-label mb-0 text-nowrap">Active Plan</label>
                    <select id="selectPlan" class="form-control app-text-primary position-relative bg-transparent cursor-pointer" style="z-index: 2; padding-right: 35px;">
                        <?php
                        require_once "server/connection.php";

                        $selectedPlan = isset($_SESSION['selectedPlan']) && $_SESSION['selectedPlan'] ? $_SESSION['selectedPlan'] : null;
                        $plans = [];
                        $userPlans = [];

                        $sql = "SELECT
                                planId as id,
                                (SELECT plans.name FROM plans WHERE plans.id = planId) as name,
                                (SELECT DATE_FORMAT(time, '%b %d, %Y') FROM plans WHERE plans.id = planId) as time,
                                (SELECT status FROM plans WHERE plans.id = planId) as status
                            FROM `user-plans` WHERE planId IN (SELECT id FROM plans WHERE status = 'active') AND userId = " . $user['id'];
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $userPlans[] = $row;
                                ?>
                                <option class="app-text-primary" value="<?php echo $row['id']; ?>" <?php echo ($selectedPlan && $row['id'] === $selectedPlan['id']) ? 'selected' : ''; ?>><?php echo $row['name']; ?></option>
                                <?php
                            }

                            $isActivePlan = false;
                            foreach ($userPlans as $up) {
                                if ($up['id'] === $selectedPlan['id']) {
                                    $isActivePlan = true;
                                    break;
                                }
                            }

                            if (!$selectedPlan || !$isActivePlan) {
                                $_SESSION['selectedPlan'] = $userPlans[0];
                            }
                        } else {
                            session_destroy();

                            header('Location: ../');
                            exit();
                        }
                        ?>
                    </select>
                    <i class="fas fa-caret-down position-absolute" style="top: 50%; transform: translateY(-50%); right: 15px; z-index: 1"></i>
                </div>
            </div>
            <div class="header-user">
                <div class="header-user-info">
                    <div class="header-user-name">
                        <span id="header-user-name"></span>
                    </div>
                    <div class="header-user-email">
                        <span id="header-user-email"></span>
                    </div>
                </div>
                <h5 class="m-0 app-text-primary">
                    <?php echo $user['firstName'] ?>
                    <?php echo $user['lastName'] ?>
                </h5>
                <a class="header-user-avatar" href="/profile.php">
                    <img id="header-user-avatar" src="/<?php echo $user['image'] ?: 'assets/images/avatar.png' ?>"
                         alt="Avatar">
                </a>
            </div>
        </div>
    </div>
</div>