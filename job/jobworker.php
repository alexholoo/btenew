<?php

// Connect to the queue
$queue = new Phalcon\Queue\Beanstalk(
    array(
        'host' => 'localhost',
        'port' => '11300'
    )
);

#$start = time();

while (1) {
    while (($job = $queue->peekReady()) !== false) {
        $pause = 0;

        $message = $job->getBody();

        $name = key($message);
        $params = current($message);

        echo "Job: $name\n";

        $file = "$name.php";

        exec('psexec -d c:/xampp/php64/php.exe ' . $file);

        $job->delete();
    }

    sleep(5);

    #if (time() - $start > 50) {
    #    break;
    #}
}
