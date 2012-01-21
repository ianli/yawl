<?php
/*
 * RAFT - RAFT Ain't For Templating
 * http://ianli.com/raft/
 *
 * Copyright 2010, Ian Li. http://ianli.com/
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php).
 *
 * RAFT is a simple implementation for templates.
 * It takes advantage of the fact that PHP, in itself,
 * is already a good templating system.
 * Building a big templating system on top of it is too much overhead.
 *
 * Versions:
 * 0.4.2beta	2010-11-17
 * - Added rafting and end_rafting.
 * - Added variable parameters to rafting which are appended to the raft.
 * - Removed begin_raft and end_raft.
 * 0.4.1beta	2010-11-17
 * - If the same id is used for begin_raft, the buffered content is appended.
 * 0.4beta	2010-11-16
 * - Added begin_raft and end_raft.
 * - Create $raft variable if it hasn't been set, so we don't have to check whether it exists.
 * 0.3	2010-11-11
 * - Added check whether $raft is set before checking whether it has a key.
 * 0.2	2010-11-09
 * - raft function always returns a value.
 * 0.1	2010-11-08
 * - Started implementation.
 */

if (!isset($raft)) {
	$raft = array();
}

function rafting($id) {
	global $raft;
	
	if (!array_key_exists($id, $raft)) {
		$raft[$id] = "";
	}
	
	if (($num_args = func_num_args()) > 1) {
		for ($i = 1; $i < $num_args; $i++) {
			$raft[$id] .= func_get_arg($i);
		}
	} else {
		ob_start();
	}
}

function end_rafting($id) {
	global $raft;
	$raft[$id] = ob_get_contents();
	ob_end_clean();
}

/**
 * The main function of RAFT.
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
 */
function raft($id) {
	global $raft;
	
	if (array_key_exists($id, $raft)) {
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