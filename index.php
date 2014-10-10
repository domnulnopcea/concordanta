<?php
    $value = isset($_GET['word']) ? $_GET['word'] : null;
    require_once './db/db_connect.php';
    require_once './utils.php';

    $query = "set names 'utf8'";
    $mysqli->query($query);

    if (isset($_POST['search_action'])) {

        Util::setUTF8Mode($mysqli);

        $value = $_POST['word'];
        header('Location: index.php?word=' . $value . '&form=all&book=all');
    }

    require_once 'header.php';

    if ($value == null) {
        $result = WordHomepage::getRandomWord($mysqli);
        $word_id = $result['word_id'];

        $result = Word::getByID($mysqli, $word_id);
        $value = $result['name'];
    }

    $form = isset($_GET['form']) ? $_GET['form'] : 'all';
    $book = isset($_GET['book']) ? $_GET['book'] : 'all';

    mb_internal_encoding("UTF-8");
    mb_regex_encoding("UTF-8");

    $cuvant_corectat = false;
    $value_forms = array();
    $value_forms_id = array();

	// cauta prima data forma exacta a cuvantului
    $word_found_result = Word::getByName($mysqli, $value);

    if ($word_found_result) {
        $word_found = $word_found_result['name'];
        $word_id = $word_found_result['id'];
    } else {

        // daca forma exacta nu a fost gasita incearca sa gasesti cuvinte care seamana cu ce a introdus utilizatorul
        $result_word_like = Word::getWordLike($mysqli, $value);
        if ($result_word_like) {
            while ($result = $result_word_like->fetch_array(MYSQLI_ASSOC)) {

                if ($value != $result['name'])
                    $cuvant_corectat = true;

                $value_forms[] = $result['name'];
                $value_forms_id[] = $result['id'];

                if (count($value_forms)) {
                    $value_forms_id = array_values($value_forms_id);
                    $word_id = $value_forms_id[0];
                    $word_found = $value_forms[0];
                    $value = $value_forms[0];
                }
            }
        }
    }

    if (isset($word_id)) {
        $result = WordForm::getWordFormsForWordID($mysqli, $word_id);

        $word_form_pks = array();
        $word_form_ids = array();

        if ($result) {
            while ($word = $result->fetch_array(MYSQLI_ASSOC)) {
                $word_form_ids[] = $word['word_form_id'];
                $word_form_pks[] = $word['id'];
            }
        }

        $result = Word::getWordsForIDs($mysqli, $word_form_ids);
        $word_forms = array();
        if ($result) {
            while ($word = $result->fetch_array(MYSQLI_ASSOC)) {
                $word_forms[] = $word['name'];
            }
        }
    }

    if (isset($word_found)) {

        $sub_menu = '<div class="result-content" style="font-size: 24px;">Cuvant cautat: <b>' . $word_found . '</b>';
        if ($cuvant_corectat)
            $sub_menu .= ' (propus)';

        for ($i = 1; $i < count($value_forms); $i++)
            $sub_menu .= ', <a href="index.php?word=' . $value_forms[$i] . '&form=all&book=all">' . $value_forms[$i] . '</a>';

        $sub_menu .= ', carte: ';
        if ($book == 'all')
            $sub_menu .= 'toate';
        else
            $sub_menu .= $book;

        //$sub_menu .= ', arata referinte pentru forma <a href="#forma_exacta">exacta</a> sau formele <a href="#forme_derivate">derivate</a>';

        $sub_menu .= '</div>';
        $sub_menu .= '<br />';
        $sub_menu .= '<div class="result-content">Arata versetele pentru ';
        $sub_menu .= '<a href="index.php?word=' . $value . '&form=all&book=all">toate formele</a>';
        $sub_menu .= ', <a href="index.php?word=' . $value . '&form=exact&book=all">forma exacta</a>';

        $sub_menu .= ' sau forme derivate: ';
        if (count($word_forms)) {
            $text = '';
            for ($i = 0; $i < count($word_forms); $i++) {
                $sub_menu .= '<a id="search_word_form_name_' . $word_form_pks[$i] . '" href="index.php?word=' . $word_forms[$i] . '&form=all&book=all">' . $word_forms[$i] . '</a> ';
                $q_sterge = 'select id from suggestion where word_form_id = ' . $word_form_pks[$i] . ' and for_delete_flag = 1 and canceled_flag = 0 limit 1';
                $r_sterge = $mysqli->query($q_sterge);
                $text = '[Sterge]';
                if ($r_sterge->num_rows)
                    $text = '[Propus pentru stergere]';
                if ($text == '[Sterge]')
                    $sub_menu .= '<a id="search_delete_word_form_' . $word_form_pks[$i] . '" href="#">' . $text . '</a>, ';
                else
                    $sub_menu .= $text . ', ';
            }
            $sub_menu = substr($sub_menu, 0, strlen($sub_menu) - 2);
            $sub_menu .= '</div>';
        } else
            $sub_menu .= 'Nu exista';

        $result = Word::getWordVersesExactForm($mysqli, $value);

        $books = array();
        $books_link = array();

        if ($result) {
            echo $sub_menu;

            echo '<br />';
            echo '<br />';
            // aici sunt afisate versetele cu forma exacta
            $versete = '<div class="result-content">';
            
            while ($verse = $result->fetch_array(MYSQLI_ASSOC)) {
                if ($book == 'all')
                    $versete .= '<div class="result_verse">' . '<i>' . $verse['book'] . ' ' . $verse['chapter'] . ':' . $verse['verse'] . '</i>' . ' ' . str_replace($word_found, '<b><i>' . $word_found . '</i></b>', $verse['text']) . '</div>';
                else if ($book == $verse['book'])
                    $versete .= '<div class="result_verse">' . '<i>' . $verse['book'] . ' ' . $verse['chapter'] . ':' . $verse['verse'] . '</i>' . ' ' . str_replace($word_found, '<b><i>' . $word_found . '</i></b>', $verse['text']) . '</div>';

                if (!in_array($verse['book'], $books)) {
                    $books[] = $verse['book'];
                    $books_link[] = '<a href="index.php?word=' . $value . '&form=all&book=' . $verse['book'] . '">' . $verse['book'] . '</a>';
                }
            }
            // Close the wraper div.
            $versete .= '</div>';
            $bookListExact = '<div style="font-size: 20px;">Arata versete din: <a href="index.php?word='
            			. $value .
            			'&form=all&book=all">toate cartile</a>, '
            			. implode($books_link, ', ')
            			. '</div><br />';

        } else echo 'Nu s-au gasit rezultate pentru ' . $value;

        // aici se vor afisa versetele din formele derivate
        if ($form == 'all') {

            if (count($word_forms)) {
                echo "<br />";

                $result = Word::getWordVersesForWords($mysqli, $word_forms);

                $text = '<div class="result-content">';
                $books = array();
                $books_link = array();

                $word_forms_to_replace = array();
                for ($i = 0; $i < count($word_forms); $i++)
                    $word_forms_to_replace[] = '<b><i>' . $word_forms[$i] . '</i></b>';

                $word_forms_replace = array();

                if ($result) {
                    while ($verse = $result->fetch_array(MYSQLI_ASSOC)) {
                        if (!in_array($verse['book'], $books)) {
                            $books[] = $verse['book'];
                            $books_link[] = '<a href="index.php?word=' . $value . '&form=all&book=' . $verse['book'] . '">' . $verse['book'] . '</a>';
                        }

                        if ($book == 'all')
                            $text .= '<div class="result_verse">' . '<i>' . $verse['book'] . ' ' . $verse['chapter'] . ':' . $verse['verse'] . '</i>' . ' ' . str_replace($word_forms, $word_forms_to_replace, $verse['text']) . '</div>';
                        else if ($book == $verse['book'])
                            $text .= '<div class="result_verse">' . '<i>' . $verse['book'] . ' ' . $verse['chapter'] . ':' . $verse['verse'] . '</i>' . ' ' . str_replace($word_forms, $word_forms_to_replace, $verse['text']) . '</div>';
                    }
                } else
                    $text .= '<div class="result_verse">Nu exista rezultate.</div>';
                
                $text .= '</div>';

                $bookListDerived = '<div style="font-size: 20px;">Arata versete din: <a href="index.php?word='
                					. $value
                					. '&form=all&book=all">toate cartile</a>, '
                					. implode($books_link, ', ')
                					. '</div><br />';
            }
        }
        
        echo '<div id="mainTabs">
  						<ul>
            				<li><a href="#tab1">Referinte forme exacte</a></li>
    						<li><a href="#tab2">Referinte forme derivate</a></li>
  						</ul>
  						<div id="tab1">
            				' . $bookListExact . '
            				<br/>
      						' . $versete . '
            			</div>
            			<div id="tab2">
      						' . $bookListDerived . '
      						<br/>
      						' . $text . '
    					</div>
			</div>';
        
    } else echo 'Nu s-a gasit nici un rezultat.';
?>

<div id="stergeForma"></div>
<?php require_once 'footer.php' ?>