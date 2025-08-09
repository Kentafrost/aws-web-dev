rem This is a script to upload zipfile that contains codes for AWS S3

rem How to invoke this script
rem 1. set the path to directory where this script is located
rem 2. And then run this script below

rem .\lambda_upload.bat

@echo off
rem How to compress files as a zip file
rem If you're using Windows11...

rem cd ..

powershell -Command "Compress-Archive -Path .\php\login\* -DestinationPath .\zip\login_php.zip -Force"
powershell -Command "Compress-Archive -Path .\php\create-html\* -DestinationPath .\zip\create-html_php.zip -Force"


rem zip file path
set ZIPFILEPATH=s3://code-apse2-bucket1313/lambda/zip

aws s3 cp .\zip\login_php.zip %ZIPFILEPATH%/login_php.zip
aws s3 cp .\zip\create-html_php.zip %ZIPFILEPATH%/create-html_php.zip

rem If you'd like to check all files in a S3 bucket for storing all zip files for Lambda functions.
aws s3 ls %ZIPFILEPATH%
