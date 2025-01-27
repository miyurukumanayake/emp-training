<div id="menu">
    <div class="menu-content py-3 d-flex flex-column">
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'admin' ? 'active' : ''?>">
            <a href="/admin">
                <i class="fas fa-user-shield"></i>
                <span>Admin users</span>
            </a>
        </div>
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'employees' ? 'active' : ''?>">
            <a href="/admin/employees.php">
                <i class="fas fa-user"></i>
                <span>Employees</span>
            </a>
        </div>
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'plans' ? 'active' : ''?>">
            <a href="/admin/plans.php">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Plans</span>
            </a>
        </div>
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'sections' ? 'active' : ''?>">
            <a href="/admin/sections.php">
                <i class="fa-solid fa-circle-half-stroke"></i>
                <span>Sections</span>
            </a>
        </div>
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'materials' ? 'active' : ''?>">
            <a href="/admin/materials.php">
                <i class="fa-solid fa-book-open"></i>
                <span>Reading Materials</span>
            </a>
        </div>
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'reports' ? 'active' : ''?>">
            <a href="/admin/reports.php">
                <i class="fa-solid fa-file-contract"></i>
                <span>Reports</span>
            </a>
        </div>
        <div class="menu-item <?php if (isset($menu)) echo $menu === 'organization' ? 'active' : ''?>">
            <a href="/admin/organization.php">
                <i class="fa-solid fa-building"></i>
                <span>Organization</span>
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