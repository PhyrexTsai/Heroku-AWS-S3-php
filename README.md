<h3>PHP heroku to AWS S3</h3>

<a href="https://heroku.com/deploy?template=https://github.com/PhyrexTsai/Heroku-AWS-S3-php.git">
<img src="https://camo.githubusercontent.com/c0824806f5221ebb7d25e559568582dd39dd1170/68747470733a2f2f7777772e6865726f6b7563646e2e636f6d2f6465706c6f792f627574746f6e2e706e67" alt="Deploy" data-canonical-src="https://www.herokucdn.com/deploy/button.png" style="max-width:100%;">
</a>

heroku need set "AWS_ACCESS_KEY_ID" and "AWS_SECRET_ACCESS_KEY" from your AWS credential.<br>
[Heroku](https://devcenter.heroku.com/articles/s3-upload-php)<br>
At AWS you need to set "Users" > "Permission" > "Attach Policy" to let your account access the S3 project.<br>
"Attach Policy" must choose "AmazonS3FullAccess".<br>
