<?php

// Connect to the queue
$queue = new Phalcon\Queue\Beanstalk(
    array(
        'host' => 'localhost',
        'port' => '11300'
    )
);

while (1) {
    while (($job = $queue->peekReady()) !== false) {

        $message = $job->getBody();
        #print_r($message);

        $class = key($message);
        $params = current($message);

        $file = __DIR__ . "/beanstalk/$class.php";

        exec('psexec -d php.exe ' . $file);

        $job->delete();
    }

    sleep(2); // 2secs
    usleep(200000); // 0.2secs
}
