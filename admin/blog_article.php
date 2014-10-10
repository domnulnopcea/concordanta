<?php
    require_once './../utils.php';

    Util::check_log_in();

    require_once './../db/db_connect.php';

    Util::setUTF8Mode($mysqli);

    require_once './include/admin_head.php';

    $id = $_GET['id'];
    $blog = Blog::getArticleByID($mysqli, $id);
?>
<div class="headerPageTextSmall">
    <?php echo $blog['title'] ?>
</div>
<div>
    Adaugat de
    <?php echo $blog['first_name'] . ' ' . $blog['last_name'] . ' in ' . $blog['date_created'] ?>
</div>
<div>
    <?php echo str_replace("\n", '<br />', $blog['content']) ?>
</div>
<?php require_once './include/admin_footer.php' ?>
