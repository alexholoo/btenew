<?php

date_default_timezone_set("America/Toronto");

// Connect to the queue
$queue = new Phalcon\Queue\Beanstalk(
    array(
        'host' => 'localhost',
        'port' => '11300'
    )
);

echo "Job worker is running\n";

prlog("Start");

$pause = 0;
$start = time();

while (1) {
    while (($job = $queue->peekReady()) !== false) {
        $pause = 0;

        $message = $job->getBody();
        $job->delete(); // delete the job from queue ASAP to prevent run it twice

        $name = key($message);
        $params = current($message);

        echo "Run Job: $name\n";

        prlog("Run Job: $name $params");

        $file = "$name.php";
        if (file_exists($file)) {
           #exec('psexec -d c:/xampp/php64/php.exe ' . $file);
            exec("c:/xampp/php64/php.exe $file $params");
        } else {
            prlog("Error: $file not found");
        }

        echo "Job End: $name\n\n";
    }

    $pause = min($pause + 1, 10);

    sleep($pause);

    if (time() - $start > 290) { // 5 minutes
        break;
    }
}

prlog("Exit\n");

function prlog($message)
{
    $filename = 'app/logs/job-worker.log';

    if (file_exists($filename) && filesize($filename) > 128*1024) {
        unlink($filename);
    }

    error_log(date('Y-m-d H:i:s').' '.$message. "\n", 3, $filename);
}
