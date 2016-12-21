<?php

if (empty($argv[1])) {
    exit("Missing job name\n");
}

$jobClass = $argv[1];

if (!file_exists("$jobClass.php")) {
    exit("No such job: $jobClass\n");
}

include __DIR__ . '/../public/init.php';

include 'classes/Job.php';
include "$jobClass.php";

if (!class_exists($jobClass)) {
    exit("Job class not found\n");
}

$job = new $jobClass;
$job->run($argv);
