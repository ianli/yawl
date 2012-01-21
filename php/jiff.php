<?php
/*
 * JIFF - JIFF Isn't a Flat File
 * Copyright 2010, Ian Li. http://ianli.com/
 * 
 * JIFF is a simple flat file implementation with the following properties.
 * - Each file represents a record.
 * - A record consists of attributes represented as key-value pairs.
 * - Attribute keys are simple names consisting of letters, numbers, and spaces.
 * - There are two kinds of attributes: single-line and multi-line. The difference is whether the value has multiple lines.
 * 
 * Attributes
 * ============
 * 
 * Single-line attributes have keys that are separated by colon. 
 * For example, an attribute with the key (name) and value (Bob) looks like this:
 * 
 * 		name: Bob
 * 
 * Multi-line attributes are a little more complicated, since the values span multiple lines. 
 * Instead, they look like the following (you'll notice the similarity to PHP's heredoc syntax 
 * http://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc):
 * 
 * 		article<<<HTML
 * 		<h2>Hello World!</h2>
 * 		<p>This is the beginning of an article.</p>
 * 		HTML
 * 
 * "article" is the key of the attribute. 
 * Notice that there are three less than signs after the key. 
 * The word after (e.g. "HTML") is the sink. The sink can be any text. Any lines of text between the two sinks is the value of the attributes.
 * 
 * Why make JIFF?
 * ================
 * 
 * Because I wanted a very simple flat file implementation. With JIFF:
 * - I don't have to worry about structure. JIFF is just flat. Yes, I lose the benefit of structures, but I benefit from simplicity. (vs. XML, JSON, YAML)
 * - I'm going to use JIFF for a very simple blogging platform, so I want HTML values to be distinct from the flat file format. (vs. XML)
 * - I don't have to escape new lines. (vs. JSON)
 * - I don't have to add tabs for multilines. (vs. YAML)
 * 
 * Versions
 * ==========
 * 0.2	2010-11-07
 * - Close the multi-line even if the last sink has whitespace at the end.
 * - Added documentation.
 * - Throw exception if file doesn't exist.
 * 0.1	2010-10-22	Started implementation.
 */

/**
 * Parses the JIFF file.
 * @param	$filename	The name of the file to open.
 * @return 	An associative array containing the values of the parsed JIFF file.
 */
function parse_jiff_file($filename) {
	if (file_exists($filename)) {
		$lines = file($filename);

		$bucket = array();
		
		$multi_key = null;
		$multi_sink = null;
		foreach($lines as $line_num => $line) {
			if ($multi_key == null) {
				if (preg_match("/^([A-Za-z0-9_ ]+)(:|<<<)(.*)$/i", $line, $matches)) {
					$key = $matches[1];
					$separator = $matches[2];
					$value = trim($matches[3]);

					if ($separator == ':') {
						// Single-line attribute
						$bucket[$key] = $value;
					} else {
						// Start parsing multi-line attribute
						$multi_key = $key;
						$multi_sink = $value;
						$bucket[$multi_key] = "";
					}
				}
			} else {
				// Processing multi-line.
				$sink = rtrim($line);
				if ($sink == $multi_sink) {
					// Done with multi-line.
					$multi_key = null;
					$multi_sink = null;
				} else {
					$bucket[$multi_key] .= $line;
				}
			}
		}
		
		return $bucket;
	} else {
		throw new Exception("$filename doesn't exist.");
	}
}
?>