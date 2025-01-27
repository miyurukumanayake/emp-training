<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/header.css">
    <link rel="stylesheet" href="/assets/css/menu.css">
    <link rel="stylesheet" href="/assets/css/footer.css">
    <?php if (isset($page)) echo "<link rel='stylesheet' href='$page.css'>\n"?>
    <script src="/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($jQueryPlugin)) echo "<script src='/assets/vendor/jquery/$jQueryPlugin'></script>\n"?>
    <script src="/assets/js/common.js"></script>
    <?php if (isset($page)) echo "<script src='$page.js'></script>\n"?>

    <title id="title"></title>
</head>
<body>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" data-modal-title></h1>
            </div>
            <div class="modal-body" data-modal-body></div>
            <div class="modal-footer">
                <button data-modal-close type="button" class="btn btn-secondary">Cancel</button>
                <button data-modal-confirm type="button" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<div id="app">
    <?php include 'components/header.php' ?>
    <?php include 'components/menu.php' ?>

    <div id="content">