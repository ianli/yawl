<?php
/*
 * YAWL - YAWL Ain't Writing Logs
 * Copyright 2010, Ian Li. http://ianli.com/
 *
 * YAWL does several things:
 * - YAWL reads a directory of JIFF files,
 *   then generates a recent file and an archive file (in JSON).
 */

require_once("yawl.config.php");
require_once("jiff.php");

// Utility functions
//====================

function _array_value($array, $key, $default) {
	return array_key_exists($key, $array) ? $array[$key] : $default;
}

// Groups the items.
// @param	$items	An array whose elements to group.
// @param	$callback	Name of a function. 
//						Function must return an object or an array.
function _group_by($items, $callback) {
	$grouped = array();
	foreach ($items as $item) {
		$groups = $callback($item);
		if (!is_array($groups)) {
			$groups = array($groups);
		}
		foreach ($groups as $group) {
			if (isset($grouped[$group])) {
				$grouped[$group][] = $item;
			} else {
				$grouped[$group] = array($item);
			}
		}
	}
	return $grouped;
}

function read_jiff_directory($directory) {
	$dirh = opendir($directory);

	$data = array();
	while ($entry = readdir($dirh)) {
		if (// File name does not start with ".", which is unsafe.
			substr($entry, 0, 1) != "." 
			// File name ends with ".jiff"
			&& preg_match("/(.+)\\.jiff$/", $entry, $matches)) {
			$filename = $directory . "/" . $entry;
			$jiff = parse_jiff_file($filename);
			$data[$matches[1]] = $jiff;
		}
	}
	
	return $data;
}

function generate_index() {
	$articles = read_jiff_directory(constant("YAWL_ARTICLES_DIR"));
	$archive = array();
	foreach ($articles as $id => $article) {
		$tags = preg_split("/\s*,\s*/i", _array_value($article, "tags", ""));
		$thumbnail = _array_value($article, "thumbnail", "");
		$archive[] = array(
			"id" => $id,
			"title" => $article["title"],
			"posted_at" => $article["posted_at"],
			"thumbnail" => $thumbnail,
			"tags" => $tags
		);
	}
	
	// Sort by posted_at
	function cmp($a, $b) {
		return strcmp($b["posted_at"], $a["posted_at"]);
	}
	usort($archive, "cmp");
	
	$fh = fopen(constant("YAWL_ARTICLES_INDEX"), "w+");
	fwrite($fh, json_encode($archive));
	fclose($fh);
}

function read_index() {
	$json = file_get_contents(constant("YAWL_ARTICLES_INDEX"), "w+");
	return json_decode($json, true);
}

function generate_recent_rss($data) {
	
}

if (array_key_exists("action", $_GET)) {
	switch($_GET["action"]) {
		case "index":
			generate_index();
			break;
	}
}

?>