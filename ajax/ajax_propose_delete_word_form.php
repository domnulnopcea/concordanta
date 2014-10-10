<?php
    require_once './../utils.php';

    require_once './../db/db_connect.php';

    $id = $_POST['id'];
    $user_id = null;
    session_start();
    if (isset($_SESSION['user_data']['id']))
        $user_id = $_SESSION['user_data']['id'];
    else
        $user_id = 'NULL';

    $date = date('Y-m-d H:m');

    $q = 'select id from suggestion where word_form_id = ' . $id . ' and for_delete_flag = 1 ';
    $r = $mysqli->query($q);

    if ($r->num_rows == 0) {
        $q = 'insert into suggestion (word_form_id, for_delete_flag) values (' . $id . ', 1)';
        $mysqli->query($q);
        $suggestion_id = $mysqli->insert_id;
        $q = 'insert into suggestion_user (suggestion_id, user_id, date_created) values (' . $suggestion_id . ", " . $user_id . ",'" . $date . "')";
        $mysqli->query($q);
    } else {
        $row = $r->fetch_array(MYSQLI_ASSOC);
        $id = $row['id'];
        $q = 'update suggestion set canceled_flag = 0, processed_flag = 0 where id = ' . $id . ' limit 1';
        $mysqli->query($q);
    }