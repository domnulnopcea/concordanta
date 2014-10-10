<?php
    /*
     * Acest script ia fiecare cuvant din tabelul word si incearca sa gaseasca formele derivate care nu au fost gasite
    * prin tabelul de la dex-online.ro
    *
    * Un exemplu:
    * pentru cuvantul 'dumnezeilor' ca forma derivata ar trebui sa fie 'Dumnezeu', 'dumnezei', etc
    *
    * Acest script va urmarii urmatorii pasi
    * - pentru cuvantul 'dumnezeilor' va lua radacina sa: de exemplu 'dumnez'
    * - va gasi toate cuvintele din tabelul word care incep cu 'dumnez'
    * - va verifica fiecare din aceste cuvinte daca exista in word form. Daca nu exista in va adauga
    */

    require_once './../db/db_connect.php';

    if (!isset($argv)) {
        require_once './../utils.php';

        Util::check_log_in();

        require_once './include/admin_head.php';
    }

    echo "Generating concordance (remaining word forms)...\n";

    mb_internal_encoding("UTF-8");
    mb_regex_encoding("UTF-8");

    $query = "set names 'utf8'";
    $mysqli->query($query);

    $query = 'select id, name from word order by id';
    echo "Creating file...\n";
    $file = "remaining_conco_1.sql";
    $fh = fopen($file, 'w') or die("can't open file");

    if ($stmt = $mysqli->prepare($query)) {

        $stmt->execute();
        $words = $stmt->get_result();

        $count = 0;
        $max_limit = 2500;
        $q_insert = 'insert into word_form(word_id, word_form_id) values ';

        while ($word = $words->fetch_array(MYSQLI_ASSOC)) {
            echo "Processing word id " . $word['id'] . ' / ' . $words->num_rows . " \r";

            $word_id = $word['id'];
            $word_name = $word['name'];
            switch (mb_strlen($word_name)) {
                case 5:
                case 6:
                case 7:
                    $to_cut = 2;
                    break;
                case 8:
                    $to_cut = 3;
                    break;
                case 9:
                    $to_cut = 4;
                    break;
            }
            if (mb_strlen($word_name > 9))
                $to_cut = 5;

            if (mb_strlen($word_name) >= 5) {
                // STEP 1
                $word_base = mb_substr($word_name, 0, mb_strlen($word_name) - $to_cut + 1) . '%';
                $q = "SELECT id from word where word.name like '" . $word_base . "'";

                if ($stmt_select = $mysqli->prepare($q)) {

                    $stmt_select->execute();
                    $result = $stmt_select->get_result();

                    if ($result->num_rows) {
                        while ($word_form = $result->fetch_array(MYSQLI_ASSOC)) {

                            $q_duplicate = 'select id, word_form_id from word_form where word_id = ' . $word_id . ' and word_form_id = ' . $word_form['id'] . ' limit 1';
                            $result_duplicate = $mysqli->query($q_duplicate);
                            if ($result_duplicate->num_rows == 0) {
                                // insert word form
                                if ($word_id != $word_form['id']) {
                                    $q_insert .= '(' . $word_id . ', ' . $word_form['id'] . '),';
                                    $count++;
                                    if ($count > $max_limit) {
                                        $count = 0;
                                        $q_insert = mb_substr($q_insert, 0, mb_strlen($q_insert) - 1);
                                        $q_insert .= ';';
                                        fwrite($fh, $q_insert . "\n");
                                        $q_insert = 'insert into word_form(word_id, word_form_id) values ';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $q_insert = mb_substr($q_insert, 0, mb_strlen($q_insert) - 1);
        $q_insert .= ';';
        fwrite($fh, $q_insert . "\n");

    }

    fclose($fh);
    echo "\nDONE";
?>

<?php if (!isset($argv)) require_once './include/admin_footer.php' ?>