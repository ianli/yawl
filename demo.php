<?php
include_once("php/yawl.php");

$yawl = new Yawl();
$yawl->action();

function raft_content() { ?>
	
	
<h1>Boats!</h1>

<p>
	This is a demo of <a href="./">YAWL Ain't Writing Logs</a>.
	All content is from Wikipedia.
</p>

<h2>Actions</h2>
<div class="span-8">
	<p>
		<a href="?action=index">Index Now!</a><br/>
		Create an index of the logs.
	</p>
</div>
<div class="span-16 last">
	<h5>Code</h5>
	<pre class="code">
$yawl = new Yawl();
$yawl->action();	
</pre>
</div>

<hr/>

<!-- Sidebar -->
<div class="span-8">
	<h3><a href="?">Index</a></h3>
	
	<?php
	$yawl = new Yawl();
	$index = $yawl->index();

	echo "<ul>";
	foreach ($index as $item) {
		$id = $item["id"];
		echo "<li>"
			. "<a href=\"?id=$id\">"
			. $item["title"]
			. "</a>"
			. "</li>";
	}
	echo "</ul>";
	?>
	
	<h5>Code</h5>
	<?php
		$php = <<<PHP
\$yawl = new Yawl();
\$index = \$yawl->index();

echo "<ul>";
foreach (\$index as \$item) {
	\$id = \$item["id"];
	echo "<li>"
	. "<a href=\"?id=\$id\">"
	. \$item["title"]
	. "</a>"
	. "</li>";
}
echo "</ul>";		
PHP;
		echo '<pre class="code">' . htmlentities($php) . '</php>';
	?>
</div>


<!-- Content -->
<div class="span-16 last">
	
	<?php
		function view_id() {
			$id = $_GET["id"];
			$yawl = new Yawl();
			$log = $yawl->log($id);

			echo <<<HTML
<h2>$log[title]</h2>
$log[body]
<p>
	<b>Posted at</b>: $log[posted_at]<br/>
	<b>Tags</b>: $log[tags]
</p>
HTML;

			$php = <<<PHP
\$id = \$_GET["id"];
\$yawl = new Yawl();
\$log = \$yawl->log(\$id);

echo <<<HTML
<h2>\$log[title]</h2>
\$log[body]
<p>
	<b>Posted at</b>: \$log[posted_at]<br/>
	<b>Tags</b>: \$log[tags]
</p>
HTML;
PHP;

			echo '<h5>Code</h5>'
				. '<pre class="code">' 
				. htmlentities($php) 
				. '</php>';
		}
		
		
		function view_all() {
			echo "<h2>Recent</h2>";

			$yawl = new Yawl();
			$index = $yawl->index();

			foreach ($index as $item) {
				$id = $item["id"];
				$log = $yawl->log($item["id"]);

				echo <<<HTML
					<h3><a href="?id=$log[id]">$log[title]</a></h3>
					$log[body]
					<p>
						<b>Posted at</b>: $log[posted_at]<br/>
						<b>Tags</b>: $log[tags]
					</p>
HTML;
			}
			
			// Show code
			//------------
			$php = <<<PHP
echo "<h2>Recent</h2>";

\$yawl = new Yawl();
\$index = \$yawl->index();

foreach (\$index as \$item) {
	\$id = \$item["id"];
	\$log = \$yawl->log(\$item["id"]);

	echo <<<HTML
		<h3><a href="?id=\$log[id]">\$log[title]</a></h3>
		\$log[body]
		<p>
			<b>Posted at</b>: \$log[posted_at]<br/>
			<b>Tags</b>: \$log[tags]
		</p>
HTML;	
}
PHP;
			echo '<h5>Code</h5>'
				. '<pre class="code">' 
				. htmlentities($php) 
				. '</php>';
		}
		
		
		if (array_key_exists("id", $_GET)) {
			view_id();
		} else {
			view_all();
		}
	
	?>
	
</div>


<?php }

$raft["title"] = "Boats!";

$raft["head"] = <<<HTML
	<style type="text/css">
	h3 a {
		padding-right:20px;
		/* color:#333 !important; */
	}
	</style>
HTML;

$raft["js"] = <<<HTML
HTML;

include("_layout.php");
?>