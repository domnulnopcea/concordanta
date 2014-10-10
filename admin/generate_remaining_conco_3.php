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

    mb_internal_encoding("UTF-8");
    mb_regex_encoding("UTF-8");

    echo "Generating concordance (remaining word forms)...\n";

    $query = "set names 'utf8'";
    $mysqli->query($query);

    $query = 'select id, name from word order by id';

    if ($stmt = $mysqli->prepare($query)) {

        $stmt->execute();
        $words = $stmt->get_result();

        $q_insert_step_2 = 'insert into word_form(word_id, word_form_id) values ';

        while ($word = $words->fetch_array(MYSQLI_ASSOC)) {
            echo "Processing word id " . $word['id'] . ' / ' . $words->num_rows . " \r";

            $word_id = $word['id'];
            $word_name = $word['name'];

            // STEP 2
            // pentru fiecare cuvant considera-l ca word_form_id. Obtine toate word_id din tabelul word_form pentru word_form_id. Apoi pentru word_id
            // uitate la toate formele si incearca sa le adaugi daca cumva nu sunt adaugate.

            $q_step_2 = "select word_id from word_form where word_form_id = " . $word_id;
            $result_step_2 = $mysqli->query($q_step_2);

            if ($result_step_2->num_rows) {
                while ($row_2 = $result_step_2->fetch_array(MYSQLI_ASSOC)) {
                    $step_2_word_id = $row_2['word_id'];

                    $qq = 'select word_form_id from word_form where word_id = ' . $step_2_word_id;
                    $rr = $mysqli->query($qq);
                    while ($row_check = $rr->fetch_array(MYSQLI_ASSOC)) {
                        $word_form_id_to_insert = $row_check['word_form_id'];
                        $q_check = 'select id from word_form where word_id = ' . $word_id . ' and word_form_id = ' . $word_form_id_to_insert . ' LIMIT 1';
                        $r_check = $mysqli->query($q_check);
                        if ($r_check->num_rows == 0) {
                            // atunci insereaza
                            if ($word_id != $word_form_id_to_insert) {
                                $q_insert_step_2 .= '(' . $word_id . ', ' . $word_form_id_to_insert . ')';
                                $mysqli->query($q_insert_step_2);
                                $q_insert_step_2 = 'insert into word_form(word_id, word_form_id) values ';
                            }
                        }
                    }
                }
            }

        }
    }

    echo "\nDONE";
?>

<?php if (!isset($argv)) require_once './include/admin_footer.php' ?>