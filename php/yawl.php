<?php
/*
 * YAWL Ain't Web Logs
 * Copyright 2010 Ian Li, http://ianli.com
 *
 * YAWL does several things:
 * - YAWL reads a directory of JIFF files,
 *   then generates a recent file and an archive file (in JSON).
 */

require_once("jiff.php");

// Utility functions
//====================

function yawl_array_value($array, $key, $default) {
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

/**
 * This is the class associated with YAWL Ain't Writing Logs.
 */
class Yawl {
	/** The directory containing the logs */
	public $logs_directory = "";
	
	/**
	 * Creates a Yawl object.
	 * @param	$dir	{String}	Optional. The directory containing the logs.
	 */
	function __construct() {
		if (func_num_args() > 0) {
			$this->logs_directory = func_get_arg(0);
		} else {
			$this->logs_directory = getcwd() . "/logs";
		}
	}
	
	/**
	 * Reads the directory containing the logs and returns their contents.
	 * 
	 */
	private function read_jiff_directory($directory) {
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

	/**
	 * Indexes the logs.
	 * @return 	{Yawl}	Returns itself for chaining.
	 */
	function index_now() {
		$dir = $this->logs_directory;
		
		$articles = $this->read_jiff_directory($dir);

		$archive = array();
		foreach ($articles as $id => $article) {
			$tags = preg_split("/\s*,\s*/i", yawl_array_value($article, "tags", ""));
			$thumbnail = yawl_array_value($article, "thumbnail", "");
			$archive[] = array(
				"id" => $id,
				"title" => $article["title"],
				"posted_at" => $article["posted_at"],
				"tags" => $tags,
				"thumbnail" => $thumbnail
			);
		}

		// Sort by posted_at
		function cmp($a, $b) {
			return strcmp($b["posted_at"], $a["posted_at"]);
		}
		usort($archive, "cmp");

		$index = $dir . "/index.json";
		$fh = fopen($index, "w+");
		fwrite($fh, json_encode($archive));
		fclose($fh);
		
		return $this;
	}
	
	/**
	 * Returns the index of the logs.
	 * @return 	{Array}	An array containing indexed information about the logs.
	 */
	function index() {
		$index = $this->logs_directory . "/index.json";
		$json = file_get_contents($index, "r");
		return json_decode($json, true);
	}
	
	/**
	 * Retrieves the log identified by $id.
	 * The log is a JIFF file.
	 * @param	$id {String}	id identifying the log
	 * @return 	{Array}	An associative array containing the log
	 *					and all of its properties.
	 */
	function log($id) {
		$filename = $this->logs_directory . "/$id.jiff";
		$log = parse_jiff_file($filename);
		$log["id"] = $id;
		return $log;
	}
	
	/**
	 * Call this to handle actions passed using the GET parameter.
	 */
	function action() {
		if (array_key_exists("action", $_GET)) {
			switch($_GET["action"]) {
				case "index":
					$this->index_now();
					break;
			}
		}
	}
}
?>