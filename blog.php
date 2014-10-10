<?php
    require_once 'header.php';
    require_once './db/db_connect.php';
    require_once './utils.php';

    $blogs = Blog::getBlogArticles($mysqli);
?>
<div class="headerPageTextSmall">Bogul www.concordanta.ro</div>
<br/>
<?php while ($blogs && $blog = $blogs->fetch_array(MYSQLI_ASSOC)): ?>
<div class="headerPageTextSmall">
    <?php echo $blog['title'] ?>
    , de
    <?php echo $blog['first_name'] . ' ' . $blog['last_name'] ?>
    ,
    <?php echo $blog['date_created'] ?>
</div>
<br/>
<div>
    <?php echo str_replace("\n", '<br />', $blog['content']) ?>
</div>
<br/>
<br/>
<?php endwhile ?>
<?php require_once 'footer.php'; ?>