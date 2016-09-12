<?php

// Connect to the queue
$queue = new Phalcon\Queue\Beanstalk(
    array(
        'host' => 'localhost',
        'port' => '11300'
    )
);

include __DIR__ . '/../public/init.php';
include __DIR__ . "/beanstalk/Job.php"; // Base Class

while (1) {
    while (($job = $queue->peekReady()) !== false) {

        $message = $job->getBody();
        #print_r($message);

        $class = key($message);
        $params = current($message);

        $file = __DIR__ . "/beanstalk/$class.php";

        if (!file_exists($file)) {
            echo "File $file not found\n";
            $job->delete();
            continue;
        }

        // please make sure the code is completely tested.
        // if there is syntax error, need to restart worker

        include_once($file);

        # This will cause a big problem, don't do this
        #
        # if (@include_once($file) === false) {
        #     echo "Syntax error in file $file\n";
        #     $job->delete();
        #     continue;
        # }

        if (!class_exists($class)) {
            echo "Class $class not found in file $file\n";
            $job->delete();
            continue;
        }

        $obj = new $class();

        if (!method_exists($obj, 'run')) {
            echo "Method $class::run() not found in class $class\n";
            $job->delete();
            continue;
        }

        try {
            $obj->run($params);
        } catch (Exception $e) {
            echo $e->getMessage(), PHP_EOL;
        }

        $job->delete();
    }

    sleep(2); // 2secs
    usleep(200000); // 0.2secs
}
