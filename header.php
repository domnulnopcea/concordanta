<?php session_start() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="Concordanta Biblica" />
	<title>Concordanta Biblica</title>
	
	<script type="text/javscript" src="lib/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="./js/jquery-2.0.2.min.js"></script>
	<script type="text/javascript" src="./js/jquery-ui-1.10.3.custom.min.js"></script>
	<script type="text/javascript" src="./js/conco.js"></script>
	
	<link rel="stylesheet" type="text/css" href="./css/jquery-ui-1.8.17.custom.css" />
	<link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap-responsive.min.css" />
	
	<link rel="stylesheet" type="text/css" href="./css/conco.css" />
	<link rel="stylesheet" type="text/css" href="./css/buttons.css" />
	<link rel="stylesheet" type="text/css" href="./css/menu.css" />
	<link rel="stylesheet" type="text/css" href="css/content.css"/>
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
    } else if (isset($_SESSION['user_data'])){
        $firstName = $_SESSION['user_data']['first_name'];
        $lastName = $_SESSION['user_data']['last_name'];
    }
    ?>
    
    <div class="navbar navbar-inverse" align="right">
  		<div style="border-radius: 0px;" class="navbar-inner">
	    	<ul class="nav" style="float: right;">
	      		<li><a href="./index.php">Acasă</a></li>
	      		<li>
		      		<?php if (isset($_SESSION['user_data'])): ?>Contul meu 
						[<a href="./admin/user_profile.php"><?php echo $firstName . ' ' . $lastName ?></a>] |
						<a href="./admin/sign_out.php">Ieşire cont</a> | <?php else: ?>
					<?php endif ?>
	      		</li>
	      		<li><a href="./sign_in.php">Conectare</a></li>
	      		<li><a href="contact.php">Contact</a></li>
	      		<li><a href="./sign_up.php">Creează cont</a></li>
	      		<li><a href="be_part_of.php">Vrei să te implici ?</a></li>
	      		<li>
		      		<form form-search name="search" method="post" action="index.php" class="form-search" style="margin: 5px 0 0;">
						<div class="input-append">
							<input type="text" class="span2 search-query"
								value="<?php if (isset($_POST['word'])) echo $_POST['word'] ?>"
								name="word">
							<button type="submit" class="btn btn-success" name="search_action">Caută</button>
						</div>
					</form>
	      		</li>
	    	</ul>
  		</div>
	</div>