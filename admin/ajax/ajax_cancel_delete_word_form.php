<?php
    require_once './../../utils.php';

    Util::check_log_in();

    require_once './../../db/db_connect.php';

    $id = $_POST['id'];

    WordForm::cancelDeletedWordForm($mysqli, $id);