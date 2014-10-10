<?php
    require_once './../db/db_connect.php';

    if (isset($argv[1]) && $argv[1] != "-c") {
        require_once './../utils.php';

        Util::check_log_in();

        require_once './include/admin_head.php';
    }

    echo "Clearing table \n";
    echo "Generating word form list...\n";

    mb_internal_encoding("UTF-8");
    mb_regex_encoding("UTF-8");

    $query = "set names 'utf8'";
    $mysqli->query($query);

    $query = 'truncate table word_form';
    $mysqli->query($query);

    $query = 'select id, name from word';

    if ($stmt = $mysqli->prepare($query)) {

        $stmt->execute();
        $words = $stmt->get_result();

        while ($word = $words->fetch_array(MYSQLI_ASSOC)) {
            $q = "select id, formNoAccent from lexem where lower(formNoAccent) = ?";
            echo "Processing unique word from list " . $word['id'] . " / " . $words->num_rows . " \r";
            if ($stmt_select = $mysqli->prepare($q)) {

                $stmt_select->bind_param("s", mb_strtolower($word['name']));
                $stmt_select->execute();
                $result = $stmt_select->get_result();

                if ($result->num_rows) {

                    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                        $lexem_id = $row['id'];

                        $q_inflected = "select distinct formNoAccent from inflectedform where lexemId = ? and lower(formNoAccent) != ?";

                        if ($stmt_inflected = $mysqli->prepare($q_inflected)) {

                            $stmt_inflected->bind_param("is", $lexem_id, mb_strtolower($word['name']));
                            $stmt_inflected->execute();
                            $result_inflected = $stmt_inflected->get_result();

                            if ($result_inflected->num_rows) {

                                while ($row_inflected = $result_inflected->fetch_array(MYSQLI_ASSOC)) {
                                    // if the inflected form exists in the list of unique words added it the table word_form
                                    $q_exists = "select id, name from word where LOWER(name) REGEXP '[[:<:]](" . mb_strtolower($row_inflected['formNoAccent']) . ")[[:>:]]' LIMIT 1";
                                    if ($stmt_exists = $mysqli->prepare($q_exists)) {
                                        $stmt_exists->execute();
                                        $result_exists = $stmt_exists->get_result();

                                        if ($result_exists->num_rows) {
                                            $row_exists = $result_exists->fetch_array(MYSQLI_ASSOC);
                                            if ($word['id'] != $row_exists['id']) {
                                                // check for duplicated
                                                $qq = 'select id from word_form where word_id = ' . $word['id'] . ' and word_form_id = ' . $row_exists['id'] . ' limit 1';
                                                $rr = $mysqli->query($qq);
                                                if (!$rr->num_rows) {
                                                    $q_insert = 'insert into word_form(word_id, word_form_id) VALUES(?, ?)';
                                                    if ($stmt_insert = $mysqli->prepare($q_insert)) {
                                                        $stmt_insert->bind_param("ii", $word['id'], $row_exists['id']);
                                                        $stmt_insert->execute();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    echo "\nDONE\n";

?>

<?php if ($argv[1] != "-c") require_once './include/admin_footer.php' ?>