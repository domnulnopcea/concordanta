<?php
    /*
     * Acest script gaseste versetele unde forma exacta a cuvintelor din tabelul word apar in versete.
    * De exemplu se vor gasi toate versetele unde apare cuvantul 'Domnul' insa forma exacta, nu si derivate,
    * cum ar fi: Domnului, Domnilor, etc
    */

    require_once './../db/db_connect.php';

    if (isset($argv[1]) && $argv[1] != "-c") {
        require_once './../utils.php';

        Util::check_log_in();

        require_once './include/admin_head.php';
    }

    echo "Clearing tables...\n";
    echo "Generating concordance (cuvinte forma exacta)...\n";

    $query = "set names 'utf8'";
    $mysqli->query($query);

    $query = 'truncate table word_verse';
    $mysqli->query($query);

    mb_internal_encoding("UTF-8");
    mb_regex_encoding("UTF-8");

    $query = 'select id, name from word';

    if ($stmt = $mysqli->prepare($query)) {

        $stmt->execute();
        $words = $stmt->get_result();

        while ($word = $words->fetch_array(MYSQLI_ASSOC)) {

            $q = "SELECT id, text FROM bible WHERE LOWER(text) REGEXP '[[:<:]](" . mb_strtolower($word['name']) . ")[[:>:]]' ";

            echo "Processing word id " . $word['id'] . ' / ' . $words->num_rows . " \r";
            if ($stmt_select = $mysqli->prepare($q)) {

                $stmt_select->execute();
                $result = $stmt_select->get_result();

                if ($result->num_rows) {
                    $count = 0;
                    $q_insert = 'insert into word_verse(word_id, verse_id) values ';
                    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                        $q_insert .= '(' . $word['id'] . ', ' . $row['id'] . '),';
                        $count++;
                        if ($count == 150) {
                            $q_insert = mb_substr($q_insert, 0, mb_strlen($q_insert) - 1);
                            $count = 0;
                            $mysqli->query($q_insert);
                            $q_insert = 'insert into word_verse(word_id, verse_id) values ';
                        }
                    }
                    $q_insert = mb_substr($q_insert, 0, mb_strlen($q_insert) - 1);
                    $mysqli->query($q_insert);
                }
            }
        }
    }

    echo $mysqli->error;
    echo "\nDONE";

?>

<?php if ($argv[1] != "-c") require_once './include/admin_footer.php' ?>