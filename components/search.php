<?php
$keyword = !isset($_POST['clearSearch']) && isset($_POST['keyword']) && $_POST['keyword'] ? $_POST['keyword'] : '';
?>
<form method="post" action="">
    <button name="search" class="btn d-none"></button>
    <div class="input-group mb-5 mt-1 px-3">
                <span class="input-group-text">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
        <input name="keyword" value="<?php echo $keyword ?>"
               class="form-control <?php echo $keyword ? 'border-end-0' : '' ?>" placeholder="Search"
               aria-label="Search">
        <?php if ($keyword) { ?>
            <span class="input-group-text bg-white cursor-pointer">
                    <button name="clearSearch"
                            class="btn btn-light btn-sm d-flex align-items-center justify-content-center"
                            style="height: 24px">
                        <i class="fa-solid fa-x fa-xs"></i>
                    </button>
                </span>
        <?php } ?>
    </div>
</form>