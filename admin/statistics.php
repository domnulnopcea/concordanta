<?php
    require_once './../utils.php';

    Util::check_log_in();
    require_once './../db/db_connect.php';
    Util::setUTF8Mode($mysqli);
    require_once './include/admin_head.php';

    $page = $_GET['page'];
    $targetPage = 'statistics';
    $users = User::getAll($mysqli);
?>

<div class="headerPageTextSmall">Statistici</div>
<div align="right">
    <?php// Util::renderPaginator($totalRows, $page, $targetPage); ?>
</div>
<table width="100%" id="table_list">
    <tr>
        <td bgcolor="#f5f5dc"><b>Utilizator</b></td>
        <td bgcolor="#f5f5dc"><b>Sugestii</b></td>
        <td bgcolor="#f5f5dc"><b>Procesare sugestii</b></td>
        <td bgcolor="#f5f5dc"><b>Cuvinte sterse</b></td>
        <td bgcolor="#f5f5dc"><b>Cuvinte setate pentru Homepage</b></td>
    </tr>
    <?php while ($user = $users->fetch_array(MYSQLI_ASSOC)): ?>
    <tr>
        <?php $stats = User::getStatistics($mysqli, $user['id']) ?>
        <td class="tdElement">
            <?php
                echo $user['first_name'] . ' ' . $user['last_name'];
                if ($user['oa_user_flag']) echo ' (Open Authentification User)'
            ?>
        </td>
        <td class="tdElement"><?php echo $stats['suggestions'] ?></td>
        <td class="tdElement"><?php echo $stats['processed'] ?></td>
        <td class="tdElement"><?php echo $stats['deleted'] ?></td>
        <td class="tdElement"><?php echo $stats['for_homepage'] ?></td>
    </tr>
    <?php endwhile ?>
</table>
<div align="right">
    <?php// Util::renderPaginator($totalRows, $page, $targetPage); ?>
</div>