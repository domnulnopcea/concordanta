<?php
    require_once './../../utils.php';

    Util::check_log_in();

    require_once './../../db/db_connect.php';

    $id = $_POST['id'];
    $date = date('Y-m-d H:m');
    $user_id = $_SESSION['user_data']['id'];
    $q = 'insert into word_for_homepage(word_id, user_id, date_created) values (' . $id . ", " . $user_id . ", '" . $date . "')";
    $mysqli->query($q);