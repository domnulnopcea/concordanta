<?php
    require_once './../utils.php';

    Util::check_log_in();

    require_once './../db/db_connect.php';
    Util::setUTF8Mode($mysqli);

    require_once './include/admin_head.php';
    $page = $_GET['page'];
    $elements_per_page = Util::ELEMENTS_PER_PAGE;

    $result_all = User::getAll($mysqli);
    $result = User::getAll($mysqli, $page, $elements_per_page);
	$totalUsers = $result_all->num_rows;
	$targetPage = "list_user";
	
	echo '<div align="right">';
		Util::renderPaginator($totalUsers, $page, $targetPage);
	echo '</div>';
   
    if ($result) {
        echo '<div class="headerPageTextSmall">Lista utilizatori</div>';
        echo '<table border="0" cellpadding="3" cellspacing="3" width="100%" id="table_list">';
        echo '<tr>';
        echo '<th bgcolor="#f5f5dc" width="40px" valign="top">Id</th>';
        echo '<th bgcolor="#f5f5dc" valign="top">Nume prenume</th>';
        echo '<th bgcolor="#f5f5dc" valign="top">Adresa email</th>';
        echo '<th bgcolor="#f5f5dc" valign="top">Super User Flag</th>';
        echo '<th bgcolor="#f5f5dc" valign="top">Open authentification user</th>';
        echo '<th bgcolor="#f5f5dc" valign="top">Data crearii</th>';
        echo '</tr>';
        while ($user = $result->fetch_array(MYSQLI_ASSOC)) {
            echo '<tr>';
            echo '<td class="tdElement">' . $user['id'] . '</td>';
            echo '<td class="tdElement">' . $user['first_name'] . ' ' . $user['last_name'] . '</td>';
            echo '<td class="tdElement">' . $user['email'] . '</td>';
            echo '<td align="center" class="tdElement">' . ($user['super_user_flag'] ? 'Da' : 'Nu') . '</td>';
            echo '<td align="center" class="tdElement">' . ($user['oa_user_flag'] ? 'Da' : 'Nu') . '</td>';
            echo '<td align="center" class="tdElement">' . $user['date_created'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else echo 'There are no users';

?>