<?php
header("Content-type: text/html; charset=utf-8");
require('../vendor/autoload.php');
define("DATA_SEPARATOR", "@#@");
$redis = new Predis\Client(getenv('REDIS_URL'));

use Aws\S3\S3Client;

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$key = getenv('AWS_ACCESS_KEY_ID')?: die('No "AWS_ACCESS_KEY_ID" config var in found in env!');
$secret = getenv('AWS_SECRET_ACCESS_KEY')?: die('No "AWS_SECRET_ACCESS_KEY" config var in found in env!');
$default_bucket = getenv('S3_BUCKET')?: die('No "S3_BUCKET" config var in found in env!');
$txt = "imagelist.txt";

$message = "";
if(!empty($_POST['submit'])){
    if(!empty($_FILES["uploadfile"])){
        $filename = $_FILES["uploadfile"]["name"];       
        $file = $_FILES["uploadfile"]["tmp_name"];
        $filetype = $_FILES["uploadfile"]["type"];
        $filesize = $_FILES["uploadfile"]["size"];
        $filedata = file_get_contents($file);
        $bucket = $_POST['bucket'];
        // insert into redis use base64
        $base64 = base64_encode(file_get_contents($file));
        $filekey = $filename.DATA_SEPARATOR.$filetype.DATA_SEPARATOR.$filesize.DATA_SEPARATOR.time().DATA_SEPARATOR.$bucket;
        $redis->mset($filekey, $base64);

        // create or update imagelist.txt
        if($s3->doesObjectExist($default_bucket, $txt)){
            // exsist
            $txtfile = $s3->getObject([
                'Bucket'    => $default_bucket,
                'Key'       => $txt
            ]);
            $txtbody = $txtfile['Body'].$filekey.PHP_EOL;
            try {
                $s3->deleteObject([
                    'Bucket' => $default_bucket,
                    'Key'    => $txt
                ]);
                $s3->putObject([
                    'Bucket' => $default_bucket,
                    'Key'    => $txt,
                    'Body'   => $txtbody,
                    'ACL'    => 'public-read-write',  // use read write
                ]);
            } catch (Aws\Exception\S3Exception $e) {
                $message .= "There was an error deleting and creating imagelist.txt.\r\n";
            }
        }else{
            // create imagelist.txt
            try {
                $s3->putObject([
                    'Bucket' => $default_bucket,
                    'Key'    => $txt,
                    'Body'   => $filekey.PHP_EOL,
                    'ACL'    => 'public-read-write',  // use read write
                ]);
            } catch (Aws\Exception\S3Exception $e) {
                $message .= "There was an error creating imagelist.txt.\r\n";
            }
        }
        
        // upload file to selected bucket
        try {
            $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $filename,
                'Body'   => $filedata,
                'ACL'    => 'public-read',  // use read
            ]);
            $message .= "Successfully uploaded file.\r\n";
        } catch (Aws\Exception\S3Exception $e) {
            $message .= "There was an error uploading the file.\r\n";
        }
    }else{
        $message .= "Something went wrong while uploading file... sorry.\r\n";
    }
}

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
					<li class="active"><a href="fileupload.php">Upload Page <span class="sr-only">(current)</span></a></li>
					<li><a href="filedownload.php">Download Page</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="row">
    	<div class="col-md-1"></div>
    	<div class="col-md-10"><h2>Upload File to AWS S3</h2></div>
    	<div class="col-md-1"></div>
    </div>
	<div class="row">
    	<div class="col-md-1"></div>
    	<div class="col-md-10">
    	<?php if($message != ''){ ?>
    	<p class="bg-warning"><?=$message?></p>
    	<?php } ?>
    	<form method="POST" action="" enctype="multipart/form-data">
    	    <div class="form-group">
                <label for="exampleInputFile">Choose Bucket</label>
                <select name="bucket" class="form-control">
                    <?php 
                    $buckets = $s3->listBuckets();
                    foreach ($buckets['Buckets'] as $bucket) {
                        echo '<option value="'.$bucket['Name'].'">'.$bucket['Name'].'</option>';
                    }
                    ?>
                </select>
            </div>
    	    <div class="form-group">
                <label for="exampleInputFile">File upload</label>
                <input type="file" name="uploadfile" id="exampleInputFile" class="form-control">
            </div>
    	    <input type="submit" name="submit" class="btn btn-primary" value="Upload Image" />
    	</form>
    	</div>
    	<div class="col-md-1"></div>
	</div>
</body>
</html>