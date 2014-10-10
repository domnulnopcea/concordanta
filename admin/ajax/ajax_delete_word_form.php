<?php
    require_once './../../utils.php';

    Util::check_log_in();

    require_once './../../db/db_connect.php';

    $id = $_POST['id'];
    $q = 'update word_form set deleted_by = ' . $_SESSION['user_data']['id'] . ', deleted_flag = 1 where id = ' . $id;
    $mysqli->query($q);