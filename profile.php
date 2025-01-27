<?php
$page = "save-admin";
$role = "profile";

require_once 'components/pag-guard.php';
require_once 'server/connection.php';
require_once 'admin/components/save-user-guard.php';

require_once 'components/pag-top.php';

include 'admin/components/save-user.php';

require_once 'components/pag-bottom.php';

