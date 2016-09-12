@echo off

start /B beanstalkd.exe -l 0.0.0.0 -p 11300 -b C:\xampp\htdocs\btenew\app\logs > C:\xampp\htdocs\btenew\app\logs\beanstalkd-error.txt

cls
echo Job Queue is running, DO NOT CLOSE THIS CONSOLE WINDOW!!
php ../job/beanstalk-worker.php
