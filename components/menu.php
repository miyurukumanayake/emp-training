<div id="menu">
    <div class="menu-content py-3 d-flex flex-column">
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'dashboard' ? 'active' : '' ?>">
            <a href="/">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>
        </div>
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'sections' ? 'active' : '' ?>">
            <a href="/sections.php">
                <i class="fa-solid fa-circle-half-stroke"></i>
                <span>Sections</span>
            </a>
        </div>
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'tasks' ? 'active' : '' ?>">
            <?php
            $userId = $_SESSION['user']['id'];
            $sql = "SELECT 
                        COUNT(*) AS overdueCount
                    FROM 
                        `user-plan-tasks` ut
                    JOIN 
                        tasks t ON ut.taskId = t.id
                    WHERE 
                        ut.userId = $userId
                        AND ut.`status` = 'in-progress'
                        AND DATE_ADD(ut.time, INTERVAL t.duration DAY) < CURDATE()";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
            }
            ?>
            <a href="/tasks.php">
                <i class="fa-solid fa-clipboard"></i>
                <span>Tasks Due</span>
                <?php if ($data['overdueCount'] > 0) { ?>
                    <span class="badge rounded-5 bg-danger"><?php echo $data['overdueCount'] ?></span>
                <?php } ?>
            </a>
        </div>
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'materials' ? 'active' : '' ?>">
            <a href="/materials.php">
                <i class="fa-solid fa-file"></i>
                <span>Reading Materials</span>
            </a>
        </div>
        <div class="flex-grow-1"></div>
        <div class="menu-item logout">
            <a href="/server/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>