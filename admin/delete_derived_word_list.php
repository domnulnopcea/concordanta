<?php
    if (isset($_POST['search'])) {
        $query = $_POST['query'];
        header('Location: delete_derived_word_list.php?page=1&word=' . $query);
    }

	require_once './../utils.php';

	Util::check_log_in();
    require_once './../db/db_connect.php';
    Util::setUTF8Mode($mysqli);
	require_once './include/admin_head.php';

    $page = $_GET['page'];
    $word = isset($_GET['word']) ? $_GET['word'] : null;

    $all_deleted_words = WordForm::getAllDeletedWords($mysqli, $word);
    $totalRows = $all_deleted_words->num_rows;

    $elements_per_page = Util::ELEMENTS_PER_PAGE;

    $deleted_words = WordForm::getDeletedWordsWithLimit($mysqli, $page, $elements_per_page, $word);
    $targetPage = 'delete_derived_word_list';

    $parameters = null;
    if ($word)
        $parameters = '&word=' . $word;

?>
<div class="headerPageTextSmall">Cuvinte derivate sterse</div>
<form name="search" action="delete_derived_word_list.php" method="post">
    <div align="right">
        <input type="text" class="inputText" value="<?php if ($word) echo $word ?>" name="query" />
        <input type="submit" value="Cauta" name="search" />
    </div>
</form>
<div align="right">
    <?php Util::renderPaginator($totalRows, $page, $targetPage, $parameters); ?>
</div>
<table width="100%" id="table_list">
    <tr>
        <td bgcolor="#f5f5dc">Cuvant</td>
        <td bgcolor="#f5f5dc">Cuvinte derivate sterse</td>
        <td bgcolor="#f5f5dc">Sters de</td>
    </tr>
    <?php while ($word = $deleted_words->fetch_array(MYSQLI_ASSOC)): ?>
        <tr>
            <td class="tdElement"><?php echo $word['base_word'] ?></td>
            <td class="tdElement"><?php echo $word['form_word'] ?> <a id="cancel_delete_word_form_<?php echo $word['id'] ?>" href="#">[Anuleaza]</a></td>
            <td class="tdElement"><?php echo $word['first_name'] . ' ' . $word['last_name']; if ($word['oa_user_flag']) echo ' (Open authentification user)'; ?></td>
        </tr>
    <?php endwhile ?>
</table>
<div align="right">
    <?php Util::renderPaginator($totalRows, $page, $targetPage, $parameters); ?>
</div>
