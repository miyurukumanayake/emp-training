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
            <div class="header-title">
                <h1 id="header-title"></h1>
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
                <a class="header-user-avatar" href="/admin/profile.php">
                    <img id="header-user-avatar" src="/<?php echo $user['image'] ?: 'assets/images/avatar.png' ?>"
                         alt="Avatar">
                </a>
            </div>
        </div>
    </div>
</div>