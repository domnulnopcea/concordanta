<?php
    require_once './../utils.php';

    Util::check_log_in();

    require_once './../db/db_connect.php';
    Util::setUTF8Mode($mysqli);
    require_once './include/admin_head.php';
    $page = $_GET['page'];
    $suggestions_arr = Suggestion::getDeleteSuggestions($mysqli, $page, Util::ELEMENTS_PER_PAGE);
    $suggestions = $suggestions_arr[0];
    $totalElements = $suggestions_arr[1];
?>
<div class="headerPageTextSmall">Sugestii pentru stergere</div>
<div align="right">
    <?php Util::renderPaginator($totalElements, $page, 'delete_suggestions') ?>
</div>
<?php $bg_color = '#CCCCCC'; ?>
<table border="0" cellpadding="3" cellspacing="3" width="100%" id="table_list">
    <tr>
        <th bgcolor="#f5f5dc" width="100px" valign="top">ID Sugestie</th>
        <th bgcolor="#f5f5dc" width="100px" valign="top">Propusa de</th>
        <th bgcolor="#f5f5dc" width="300px" valign="top">Cuvantul de baza</th>
        <th bgcolor="#f5f5dc" valign="top">Forma cuvantului</th>
        <th bgcolor="#f5f5dc" valign="top">Optiuni</th>
    </tr>
    <?php while ($suggestions && $suggestion = $suggestions->fetch_array(MYSQLI_ASSOC)): ?>
    <tr>
        <td class="tdElement"><?php echo $suggestion['id'] ?></td>
        <td class="tdElement">
            <?php
            $userInfo = Suggestion::getUserForSuggestionID($mysqli, $suggestion['id']);
            if ($userInfo)
                echo $userInfo['first_name'] . ' ' . $userInfo['last_name'];
            else
                echo 'Anonim';
            ?>
        </td>
        <td class="tdElement"><?php
            $word_form_id = $suggestion['id'];
            $word_form_row = WordForm::getByID($mysqli, $suggestion['word_form_id']);
            $word = Word::getByID($mysqli, $word_form_row['word_id']);
            echo $word['name'];
            ?>
        </td>
        <td class="tdElement">
            <?php
            $word = Word::getByID($mysqli, $word_form_row['word_form_id']);
            echo $word['name'];
            ?>
        </td>
        <td class="tdElement"><?php if ($suggestion['canceled_flag'] == 1 || $suggestion['processed_flag'] == 0): ?>
            <input type="button" value="Confirma stergere" name="confirm"
                   id="confirm_suggestion_<?php echo $suggestion['id'] ?>_<?php echo $suggestion['word_form_id'] ?>"/>
            <?php else: ?> <input type="button" disabled="disabled"
                                  value="Stergere confirmata"
                                  id="confirm_suggestion_<?php echo $suggestion['id'] ?>_<?php echo $suggestion['word_form_id'] ?>"/>
            <?php endif ?> <?php if ($suggestion['canceled_flag'] != 1): ?> <input
            type="button" value="Anuleaza" name="cancel"
            id="cancel_suggestion_<?php echo $suggestion['id'] ?>_<?php echo $suggestion['word_form_id'] ?>"/>
            <?php else: ?> <input type="button" disabled="disabled"
                                  value="Sugestie anulata"
                                  id="cancel_suggestion_<?php echo $suggestion['id'] ?>_<?php echo $suggestion['word_form_id'] ?>"/>
            <?php endif ?>
        </td>
    </tr>
    <?php endwhile ?>
</table>
<?php require_once './include/admin_footer.php' ?>