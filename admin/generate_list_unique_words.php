<?php
    /*
    * Acest script ia fiecare verset din Biblie si il sparge in cuvinte.
    * Fiecare cuvant este luat si introdus in tabelul word. Aici nu se vor introduce duplicate.
    * Deci in tabelul word vom avea cuvintele unice din Biblie
    *
    * Cel mai bine pentru progres este ca acest script sa se execute din linia de comanda asa
    * php generate_list_unique_words.php -c
    * Parametrul -c spune scriptului ca e rulat din linia de comanda
    */

    require_once './../db/db_connect.php';

    if (!isset($argv)) {
        require_once './../utils.php';

        check_log_in();

        require_once './include/admin_head.php';
    }

    echo "Dropping unique words list...\n";

    $query = "set names 'utf8'";
    $mysqli->query($query);

    $query = "truncate table word\n";
    $mysqli->query($query);

    echo "Generating list...\n";

    $query = "select id, verse, book, chapter, text from bible";

    mb_internal_encoding("UTF-8");
    mb_regex_encoding("UTF-8");

    $max = 0;
    $q_insert_word = 'insert into word(name) values ';
    $words_unique = array();
    $param_arr = array();
    if ($stmt = $mysqli->prepare($query)) {

        $stmt->execute();
        $verses = $stmt->get_result();

        $search_arr = array(',', ':', ';', '\!', '\?', '\"', '„', '”', '\.', '\(', '\)', '\.\.\.', '…', '–');

        while ($verse = $verses->fetch_array(MYSQLI_ASSOC)) {
            echo "Processing verse " . $verse['book'] . " - " . $verse['chapter'] . " \r";
            $verse_text = strip_tags($verse['text']);

            for ($i = 0; $i < count($search_arr); $i++) {
                $verse_text = mb_eregi_replace($search_arr[$i], " ", $verse_text);
            }

            $words = explode(" ", $verse_text);

            // remove duplicates
            $words = array_unique($words);

            // re index
            $words = array_values($words);

            // check for duplicates in the db
            for ($i = 0; $i < count($words); $i++) {
                $q_duplicate = "select LOWER(name) from word where LOWER(name) REGEXP '[[:<:]](" . mb_strtolower($words[$i]) . ")[[:>:]]'";
                if ($stmt_duplicate = $mysqli->prepare($q_duplicate)) {
                    $stmt_duplicate->execute();

                    $result = $stmt_duplicate->get_result();
                    if (!$result->num_rows) {
                        $value_small = mb_strtolower($words[$i]);
                        if (!in_array($value_small, $words_unique)) {
                            if (mb_strlen($words[$i]) >= 2) {
                                $words_unique[] = $value_small;
                                $param_arr[] = "('" . $words[$i] . "')";
                                $max++;
                            }
                        }
                    }
                }
            }

            if ($max > 250) {
                $max = 0;
                $q_insert_word .= implode($param_arr, ', ');
                $q_insert_word = mb_substr($q_insert_word, 0, mb_strlen($q_insert_word));
                $mysqli->query($q_insert_word);
                $q_insert_word = 'insert into word(name) values ';
                $words_unique = array();
                $param_arr = array();
            }
        }
        $q_insert_word .= implode($param_arr, ', ');
        $q_insert_word = mb_substr($q_insert_word, 0, mb_strlen($q_insert_word));
        $mysqli->query($q_insert_word);
    }

    echo "\nDONE";
?>

<?php if (!isset($argv)) require_once './include/admin_footer.php' ?>