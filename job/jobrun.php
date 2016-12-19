<?php

if (empty($argv[1])) {
    exit('Missing job name');
}

$jobClass = $argv[1];

include __DIR__ . '/../public/init.php';

include 'classes/Job.php';
include "$jobClass.php";

if (class_exists($jobClass)) {
    $job = new $jobClass;
    $job->run($argv);
}
