<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <script type="text/javascript" src="./../js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="./../js/menu.js"></script>
    <script type="text/javascript" src="./../js/jquery-ui-1.8.17.custom.min.js"></script>
    <script type="text/javascript" src="./../js/admin.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="./../css/menu.css"/>
    <link rel="stylesheet" type="text/css" href="./../css/conco.css"/>
    <link rel="stylesheet" type="text/css" href="./../css/buttons.css"/>
    <link rel="stylesheet" type="text/css"
          href="./../css/jquery-ui-1.8.17.custom.css"/>
    <script type="text/javascript">

        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-30732933-1']);
        _gaq.push(['_trackPageview']);

        (function () {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();
    </script>
</head>
<body>
<?php
    if (isset($_SESSION['user_data']['google_auth'])) {
        $firstName = $_SESSION['user_data']['namePerson/first'];
        $lastName = $_SESSION['user_data']['namePerson/last'];
    } else if (isset($_SESSION['user_data'])) {
        $firstName = $_SESSION['user_data']['first_name'];
        $lastName = $_SESSION['user_data']['last_name'];
    }
?>
<div class="menu" id="menu">
    <ul>
        <span id="first_menu">
            <li><a href="#">Home<span class="arrow"></span></a>
                <ul>
                    <li><a href="http://www.concordanta.ro">concordanta.ro</a></li>
                    <li><a href="index.php">Panou principal</a></li>
                </ul>
            </li>
            <?php if (isset($_SESSION['user_data']['super_user_flag']) && $_SESSION['user_data']['super_user_flag'] == 1): ?>
                <li><a href="#">Concordanta<span class="arrow"></span></a>
                    <ul>
                        <li><a href="./unique_word_list.php?page=1">Lista cuvinte unice</a></li>
                        <li><a href="./delete_derived_word_list.php?page=1">Cuvinte derivate sterse</a></li>
                    </ul>
                </li>
                <li><a href="#">Utilizatori<span class="arrow"></span></a>
                    <ul>
                        <li><a href="./list_user.php?page=1">Lista utilizatori</a></li>
                        <li><a href="./statistics.php">Statistici</a></li>
                    </ul>
                </li>
                <li><a href="#">Sugestii<span class="arrow"></span></a>
                    <ul>
                        <li><a href="./delete_suggestions.php?page=1">Sugestii pentru stergere</a>
                        </li>
                        <!-- <li><a href="../add_suggestions.php">Sugestii pentru adaugare</a> -->
                        </li>
                    </ul>
                </li>
                <li><a href="#">Blog<span class="arrow"></span></a>
                    <ul>
                        <li><a href="./add_blog.php">Adauga articol</a></li>
                        <li><a href="./list_blog.php">Lista articole</a></li>
                    </ul>
                </li>
            <?php endif ?>
            <li><a href="#"><?php echo $firstName . ' ' . $lastName ?><span class="arrow"></span></a>
                <ul>
                    <li><a href="./user_info.php">Profilul meu</a></li>
                    <li><a href="./statistics.php?page=1">Statistici</a></li>
                    <li><a href="./change_password.php">Schimba parola</a></li>
                    <li><a href="./sign_out.php">Deconectare</a></li>
                </ul>
            </li>
        </span>
    </ul>
</div>