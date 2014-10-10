<?php
    require_once './../utils.php';

    if (isset($_POST['word'])) {
        $word_to_search = Util::cleanRegularInputField($_POST['word']);
        if ($word_to_search != '')
            header('Location: unique_word_list.php?word=' . $word_to_search);
    }

    Util::check_log_in();

    require_once './../db/db_connect.php';
    Util::setUTF8Mode($mysqli);
    require_once './include/admin_head.php';

    $word_to_search = null;
    if (isset($_GET['word']))
        $word_to_search = $_GET['word'];

// Get the number of total rows in the word table.
    $q = "SELECT COUNT(*) as totalRows FROM word ";
    $part_link_pagination = '';
    if ($word_to_search) {
        $q .= " where name like '%" . $word_to_search . "%'";
        $part_link_pagination = 'word=' . $word_to_search;
    }
    $st = $mysqli->prepare($q);
    $st->execute();
    $result = $st->get_result()->fetch_array(MYSQLI_ASSOC);

// Holds the number of total rows in the word table.
    $totalRows = $result['totalRows'];

// Hold the name of the page.
    $targetPage = "unique_word_list";

    $elements_per_page = Util::ELEMENTS_PER_PAGE;
    $page = $_GET['page'];
    if ($page)
        $start = ($page - 1) * $elements_per_page;
    else
        $start = 0;

// Query the table for the specific number of rows (in this case 50).
    $q1 = "SELECT * FROM word ";
    if ($word_to_search)
        $q1 .= " where name like '%" . $word_to_search . "%'";

    $q1 .= " LIMIT ?, ?";

    $stmt_insert = $mysqli->prepare($q1);
    $stmt_insert->bind_param("ii", $start, $elements_per_page);

// The result will be iterated later.
    $stmt_insert->execute();
    $resultToShow = $stmt_insert->get_result();
?>
<div style="height: 6px;"></div>
<form action="unique_word_list.php" method="POST">
    <div align="right">
        <input value="<?php if (isset($word_to_search)) echo $word_to_search ?>" type="text" name="word"
               class="inputText"/> <input name="search" type="submit" value="Cauta"/>
    </div>
</form>

<div align="right">
    <?php Util::renderPaginator($totalRows, $page, $targetPage, $part_link_pagination); ?>
</div>

<?php $bg_color = '#CCCCCC'; ?>
<table border="0" cellpadding="3" cellspacing="3" width="100%">
    <tr>
        <th bgcolor="#f5f5dc" width="100px" valign="top">ID cuvant</th>
        <th bgcolor="#f5f5dc" width="300px" valign="top">Cuvant</th>
        <th bgcolor="#f5f5dc" valign="top">Forme ale cuvantului</th>
    </tr>
    <?php while ($row = $resultToShow->fetch_array(MYSQLI_ASSOC)): ?>
    <?php if ($bg_color == '#CCCCCC')
        $bg_color = '#DDDDDD';
    else $bg_color = '#CCCCCC' ?>
    <tr bgcolor="<?php echo $bg_color ?>">
        <td valign="top"><?php echo $row['id'] ?></td>
        <td valign="top"><?php echo $row['name'] ?> <?php
            $adaugat_for_hp = false;
            $q = 'select id from word_for_homepage where word_id = ' . $row['id'] . ' limit 1';

            $r = $mysqli->query($q);
            if ($r->num_rows)
                $adaugat_for_hp = true;
            ?> <?php if ($adaugat_for_hp): ?> <span>[Setat pentru Homepage]</span> <?php else: ?> [<a
                id="for_hp_<?php echo $row['id'] ?>" title="Propune acest cuvant sa apara pe prima pagina" href="#">For Homepage</a>] <?php endif ?>
        </td>
        <td valign="top"><?php
            $qq = 'select word_form.id, word_id, word_form_id, word.name, word_form.deleted_flag ' .
                'from word_form ' .
                'left join word on word.id = word_form.word_form_id ' .
                'where word_id = ' . $row['id'] . ' ' .
                'and word_form.deleted_flag IS NULL';
            $rr = $mysqli->query($qq);
            $forms = array();
            $value = '';
            while ($row_form = $rr->fetch_array(MYSQLI_ASSOC)) {
                if ($row_form['deleted_flag'] == 1)
                    $value = 'Sters';
                else
                    $value = 'x';
                $text = '<span id="word_form_name_' . $row_form['id'] . '">' . $row_form['name'] . '</span>';
                if ($value == 'x')
                    $text .= '<a id="delete_word_form_' . $row_form['id'] . '" title="Sterge forma derivata" href="#">[' . $value . ']</a>';
                else
                    $text .= '[Sters]';
                $forms[] = $text;
            }
            echo implode(', ', $forms);
            ?>
        </td>
    </tr>
    <?php endwhile ?>
</table>
<div align="right">
    <?php Util::renderPaginator($totalRows, $page, $targetPage, $part_link_pagination); ?>
</div>
<input type="hidden" id="dont_ask_delete_derivate_form_flag" value="<?php echo $_SESSION['user_data']['dont_ask_delete_derivate_form_flag'] ?>" />
<div id="stergeForma"></div>
<div id="forHomepage"></div>
