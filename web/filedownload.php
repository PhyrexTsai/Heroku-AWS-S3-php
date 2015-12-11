<?php
header("Content-type: text/html; charset=utf-8");
require('../vendor/autoload.php');
$redis = new Predis\Client(getenv('REDIS_URL'));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Homework 3</title>
<script	src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script	src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
	integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
	crossorigin="anonymous">
<!-- Latest compiled and minified JavaScript -->
<script
	src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
	integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
	crossorigin="anonymous"></script>
</head>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li><a href="fileupload.php">Upload Page</a></li>
					<li class="active"><a href="filedownload.php">Download Page <span class="sr-only">(current)</span></a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="row">
    	<div class="col-md-1"></div>
    	<div class="col-md-10"><h2>Files in Redis</h2></div>
    	<div class="col-md-1"></div>
    </div>
	<div class="row">
        <div class="col-md-1"></div>
    	<div class="col-md-10">     	   
    	    <table class="table">
                <tr>
                    <th>Bucket</th>
                    <th>File Name</th>
                    <th>File type</th>
                    <th>File size</th>
                    <th>File</th>
                </tr>
            <?php 
               $arList = $redis->keys("*");
        	   foreach($arList as $num => $key){
        	       $splitKey = preg_split("/@#@/", $key);
        	       
        	       $filedata = $redis->get($key);
        	       $bucket = $splitKey[4];
        	       $filename = $splitKey[0];
        	       $filetype = $splitKey[1];
        	       $filesize = $splitKey[2];
        	       echo '<tr>';
        	       echo '<td>'.$bucket.'</td>';
        	       echo '<td>'.$filename.'</td>';
        	       echo '<td>'.$filetype.'</td>';
        	       echo '<td>'.$filesize.'</td>';
        	       echo '<td><a href="https://s3-us-west-2.amazonaws.com/'.$bucket.'/'.$filename.'"><img src="data:image/jpeg;base64,' . $filedata . '" width="100" /></a></td>';
        	       echo '</tr>';
        	   }
            ?>
    	    </table>
    	
    	</div>
    	<div class="col-md-1"></div>
	</div>
</body>
</html>