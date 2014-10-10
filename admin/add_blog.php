<?php
    require_once './../utils.php';
    require_once './../db/db_connect.php';

    Util::check_log_in();
    Util::setUTF8Mode($mysqli);

    require_once './include/admin_head.php';

    if (isset($_POST['add'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];

        $query = 'insert into blog(title, content, date_created, created_by) values (?, ?, ?, ?)';
        $date = date('Y-m-d H:m');
        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("sssi", $title, $content, $date, $_SESSION['user_data']['id']);
            $stmt->execute();
        }
        header('Location: ./list_blog.php');
    }
?>
<form name="blog" method="post" action="add_blog.php">
    <table>
        <tr>
            <td colspan="2">
                <div class="headerPageTextSmall">Adauga un nou articol pe blog</div>
            </td>
        </tr>
        <tr>
            <td>Titlu</td>
            <td><input name="title" type="text" class="inputText"/></td>
        </tr>
        <tr>
            <td valign="top">Continut</td>
            <td><textarea class="inputTextAreaLarge" style="height: 400px"
                          name="content"></textarea></td>
        </tr>
        <tr>
            <td></td>
            <td><input class="button" type="submit" value="Adauga articol" name="add"/></td>
        </tr>
    </table>
</form>
<?php require_once './include/admin_footer.php' ?>
