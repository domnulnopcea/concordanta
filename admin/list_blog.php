<?php
    require_once './../utils.php';

    Util::check_log_in();

    require_once './../db/db_connect.php';
    Util::setUTF8Mode($mysqli);

    require_once './include/admin_head.php';

    $blogs = Blog::getBlogArticles($mysqli);
    $bg_color = '#CCCCCC'
?>
<span class="headerPageTextSmall">Lista articole</span>
<table border="0" cellpadding="3" cellspacing="3" width="100%">
    <tr>
        <td bgcolor="#f5f5dc">Titlu</td>
        <td bgcolor="#f5f5dc">Continut</td>
        <td bgcolor="#f5f5dc">Adaugat in</td>
        <td bgcolor="#f5f5dc">Adaugat de</td>
    </tr>
    <?php while ($blogs && $blog = $blogs->fetch_array(MYSQLI_ASSOC)): ?>
    <?php if ($bg_color == '#CCCCCC') $bg_color = '#DDDDDD'; else $bg_color = '#CCCCCC' ?>
    <tr bgcolor="<?php echo $bg_color ?>">
        <td width="50%"><a
            href="blog_article.php?id=<?php echo $blog['id'] ?>"><?php echo $blog['title'] ?>
        </a></td>
        <td width="30%"><?php echo substr($blog['content'], 0, 100) ?></td>
        <td><?php echo $blog['date_created'] ?></td>
        <td><?php echo $blog['first_name'] . ' ' . $blog['last_name'] ?></td>
    </tr>
    <?php endwhile ?>
</table>
<?php require_once './include/admin_footer.php' ?>