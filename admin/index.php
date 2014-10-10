<?php
    require_once './../utils.php';

    Util::check_log_in();

    require_once './../db/db_connect.php';
    Util::setUTF8Mode($mysqli);
    require_once './include/admin_head.php';

?>

<?php require_once './include/admin_footer.php' ?>