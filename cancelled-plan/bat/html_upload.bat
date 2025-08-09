rem This is a script to upload zipfile that contains codes for AWS S3

rem How to invoke this script
rem 1. set the path to directory where this script is located
rem 2. And then run this script below

rem .\lambda_upload.bat

@echo off
rem How to compress files as a zip file
rem If you're using Windows11...

rem cd ..

rem zip file path
set HTMLFILEPATH=s3://web-apse2-bucket1313
aws s3 cp ./html/index.html   %HTMLFILEPATH%/index.html
aws s3 cp ./html/error.html %HTMLFILEPATH%/error.html
aws s3 cp ./html/top.html %HTMLFILEPATH%/top.html

rem If you'd like to check all files in a S3 bucket for storing all zip files for Lambda functions.
aws s3 ls %HTMLFILEPATH%
