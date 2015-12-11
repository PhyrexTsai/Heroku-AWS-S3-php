@ECHO OFF
SET BIN_TARGET=%~dp0/../heroku/heroku-buildpack-php/bin/heroku-php-nginx
bash "%BIN_TARGET%" %*
