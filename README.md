<h3>PHP heroku to AWS S3</h3>
heroku need set "AWS_ACCESS_KEY_ID" and "AWS_SECRET_ACCESS_KEY" from your AWS credential.<br>
[Heroku](https://devcenter.heroku.com/articles/s3-upload-php)<br>
At AWS you need to set "Users" > "Permission" > "Attach Policy" to let your account access the S3 project.<br>
"Attach Policy" must choose "AmazonS3FullAccess".<br>
