<?php

// Connect to the queue
$queue = new Phalcon\Queue\Beanstalk(
    array(
        'host' => 'localhost',
        'port' => '11300'
    )
);

prlog("Start");

$pause = 0;
$start = time();

while (1) {
    while (($job = $queue->peekReady()) !== false) {
        $pause = 0;

        $message = $job->getBody();

        $name = key($message);
        $params = current($message);

        prlog("Run Job: $name");

        $file = "$name.php";
        if (file_exists($file)) {
           #exec('psexec -d c:/xampp/php64/php.exe ' . $file);
            exec('c:/xampp/php/php.exe ' . $file);
        } else {
            prlog("Error: $file not found");
        }

        $job->delete();
    }

    $pause = min($pause + 1, 10);

    sleep($pause);

    if (time() - $start > 50) {
        break;
    }
}

prlog("Exit\n");

function prlog($message)
{
    $filename = 'app/logs/job-worker.log';
    error_log(date('Y-m-d H:i:s').' '.$message. "\n", 3, $filename);
}
