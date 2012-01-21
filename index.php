<?php function raft_content() { ?>

<h1>YAWL <span class="sub">Ain't Writing Logs</span></h1>
<div id="bd" class="span-18">
<p>
	YAWL is an easy-to-use blog system. It has the following properties:
</p>
<ul>
	<li>It is lightweight.</li>
	<li>The YAWL library is contained in one file.</li>
</ul>
<p>
	To make YAWL lightweight, it makes a few concessions:
</p>
<ul>
	<li>There is no web interface to generate posts.</li>
	<li>Writing posts is in the JIFF format.</li>
</ul>

<h2>Suggested libraries</h2>
<p>
	To make YAWL easier to use as a blog system, I suggest using the following libraries.
</p>

<p>
	<i>RAFT Ain't For Templating</i> is a simple templating system.
</p>

<p>
	<i>Textile</i>
</p>

<h2>Log Format</h2>

<p>
	The log format uses JIFF, a flat file format.
	YAWL recommends the following parameters in the flat file.
</p>

<pre class="code">
title: Title of the post
posted_at: 2010-11-15
tags: tag1,tag2
thumbnail: http://url
image: http://url
format: textile
body&lt;&lt;&lt;TXT
Body goes here.
TXT
</pre>

<p>
	All logs are stored in the folder <code>logs</code>.
</p>

<h2>Indexing</h2>

<p>
	YAWL generates an <code>index.json</code> file containing an index of all the logs.
	The index file is stored in the same location as the logs.
	The following parameters from the JIFF file are indexed.
</p>

<ul>
	<li>id - derived from the filename of the JIFF file (without the extension).</li>
	<li>title</li>
	<li>posted_at</li>
	<li>tags</li>
	<li>thumbnail (optional)</li>
</ul>

<h3>Retrieving Index via PHP</h3>
<h3>Retrieving Index via JSON</h3>

<h2>Recent</h2>
</div>
<div class="span-6 last">
	<ul id="toc">
	</ul>
</div>


<?php } ?>
<?php

$raft["title"] = "YAWL Ain't Writing Logs";

$raft["head"] = <<<HTML
<style type="text/css">
h1 span.sub,
h2 span.sub,
h3 span.sub,
h4 span.sub,
h5 span.sub,
h6 span.sub {
	color:#ccc;
}
</style>
HTML;

$raft["js"] = <<<HTML
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript">
	// Generates a table of contents from the headers in the page.
	$("#bd h2").each(function(index) {
		var text = $(this).text();
		var anchor = text.toLowerCase().replace(/[^a-z0-9_-]+/g, "_");
	
		// Add the anchor before the header.
		$(this).before('<a name="' + anchor + '"></a>');
	
		// Add an item in the table of contents.
		$("ul#toc").append('<li><a href="#' + anchor + '">' + text + '</a></li>');
	});
</script>
</script>
HTML;

include("_layout.php");
?>
