<?php
    require_once './../../utils.php';

    Util::check_log_in();

    require_once './../../db/db_connect.php';

    $id = $_POST['id'];
    $word_form_id = $_POST['word_form_id'];
    $q = 'update suggestion set canceled_flag = 0, processed_flag = 1, processed_by = ' . $_SESSION['user_data']['id'] . ' where id = ' . $id . ' limit 1';
    $mysqli->query($q);

    $q = 'update word_form set deleted_flag = 1 where id = ' . $word_form_id . ' limit 1';
    $mysqli->query($q);
