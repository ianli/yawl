<?php include_once("php/raft.php"); ?>
<!DOCTYPE html> 
<!--
 * A Blank Start design
 * http://ianli.com/templates/ablankstart/
 *
 * Copyright 2010, Ian Li (http://ianli.com)
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php).
--> 
<html lang="en"> 
<head> 
	<meta charset="utf-8" /> 
	<title><?= raft("title") ?></title>
 
	<!--[if IE]>
		<script src="js/html5shiv.js"></script><![endif]--> 
	<!--[if lt IE 9]>
		<script src="js/IE9.js" type="text/javascript"></script><![endif]--> 

	<link rel="stylesheet" href="css/cw15gw20cc24/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="css/cw15gw20cc24/print.css" type="text/css" media="print" />
	<!--[if IE]><link rel="stylesheet" href="css/cw15gw20cc24/ie.css" type="text/css" media="screen, projection" /><![endif]-->
	
	<link rel="stylesheet" href="css/ablankstart.css" type="text/css" />

	<?= raft("head") ?>
</head>
<body>

<div id="wrap">
<div id="bd_wrap">
	<div class="container">
		<?= raft("content") ?>
	</div>
</div>

<div id="ft_wrap">
	<div class="container">
		<?= raft("ft") ?>
	</div>
</div>
</div>

<?= raft("js") ?>

</body>
</html>
