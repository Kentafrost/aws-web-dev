rem This is a script to upload zipfile that contains codes for AWS S3

rem How to invoke this script
rem 1. set the path to directory where this script is located
rem 2. And then run this script below

rem .\lambda_upload.bat

@echo off
rem How to compress files as a zip file
rem If you're using Windows11...

rem cd ..

powershell -Command "Compress-Archive -Path .\php-lambda-layer\* -DestinationPath .\zip\php-lambda-layer.zip -Force"