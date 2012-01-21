<?php
/*
 * RAFT - RAFT Ain't For Templating
 * Copyright 2010, Ian Li. http://ianli.com/
 *
 * RAFT is a simple implementation for templates.
 * It takes advantage of the fact that PHP, in itself,
 * is already a good templating system.
 * Building a big templating system on top of it is too much overhead.
 * 
 * Here's how RAFT works:
 * 1. The user creates a layout in PHP by including raft.php
 *    and using the raft function.
 *    For example, here's a layout file (layout.php):
 *
 * 		<?php include_once("raft.php"); ?>
 *		<html>
 *		<head>
 *			<title><?php echo raft("title"); ?></title>
 *		</head>
 *		<body>
 *			<?php raft("content"); ?>
 *		</body>
 *		</html>
 *
 * 2. The user creates a content page; again, in PHP.
 *    The user assigns values to an associative array $raft.
 *    Or creates functions that start with "raft_".
 *    For example:
 *
 *		<?php
 *		$raft["title"] = "My Web Page.";
 *
 *      function raft_content() {
 *			echo "Hello World!";
 *		}
 *
 *		include("layout.php");
 *		?>
 * 3. That's it!
 *
 * Versions:
 * 0.3	2010-11-11
 * Added check whether $raft is set before checking whether it has a key.
 * 0.2	2010-11-09
 * raft function always returns a value.
 * 0.1	2010-11-08
 * Started implementation.
 */

function raft($id) {
	global $raft;
	
	if (isset($raft) && array_key_exists($id, $raft)) {
		// $id is $raft, so just print that.
		return $raft[$id];
	} else {
		// $id is not an element of $raft, so it might be a function prefixed "raft_".
		$fn = "raft_$id";
		
		if (function_exists($fn)) {
			// If there is more than one argument to raft,
			// grab the arguments except the first one (which is $id).
			// These arguments are passed to the function identified by "raft_$id"
			$args = array();
			if (($num_args = func_num_args()) > 1) {
				$args = func_get_args();
				$args = array_slice($args, 1);
			}
			
			// Call the function and pass the arguments also.
			return call_user_func_array($fn, $args);
		}
	}
}
?>