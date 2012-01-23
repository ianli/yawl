<!DOCTYPE html>
<html> 
<head>
	<title><?= raft("page.title") ?></title>
	
	<!-- UTF-8 (Unicode) encoding -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <!-- HTML5 shim, for IE6-8 support of HTML elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<!-- Bootstrap -->
	<link rel="stylesheet" href="http://twitter.github.com/bootstrap/1.4.0/bootstrap.min.css" />
	
	<link rel="stylesheet" href="/yawl/css/default.css" type="text/css" />
</head>
<body>

<div class="container">
  <div class="row">
    <div class="offset3 span10">
      <div class="hero-unit">
        <h1>Boats etc.</h1>
        <p>
          A demo of <a href="http://github.com/ianli/yawl/">YAWL</a>,
          a very simple blogging platform in PHP.
        </p>
      </div>
      
      <div class="row">
        <div class="offset1 span8">
          <ul class="pills">
            <li class="<?= raft('page.permalink') == 'index.html' ? 'active' : '' ?>">
              <a href="/yawl/">Home</a>
            </li>
          </ul>
        </div>
      </div>
      
      <div class="row">
        <div class="offset1 span8" id="content">
          <?= raft("page.content") ?>
        </div>
      </div>
      
      <footer>
        <p>
          This demo is generated by YAWL.
          <a href="http://github.com/ianli/yawl/">Get the source code</a>.
        </p>
        <p>
          Copyright 2012 <a href="http://ianli.com">Ian Li</a>.
          Licensed under <a href="http://www.opensource.org/licenses/mit-license.php">the MIT License</a>.
        </p>
      </footer>
    </div>
  </div>
</div>

<!-- GitHub Ribbon -->
<a href="http://github.com/ianli/yawl/"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://a248.e.akamai.net/assets.github.com/img/71eeaab9d563c2b3c590319b398dd35683265e85/687474703a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f677261795f3664366436642e706e67" alt="Fork me on GitHub"></a>

</body>
</html>
