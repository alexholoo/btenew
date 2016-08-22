<?php

class MainTask extends \Phalcon\Cli\Task
{
    public function mainAction()
    {
        echo __METHOD__, PHP_EOL;

        print_r($this->config->database->host);
    }

    /**
     * @param array $params
     */
    public function testAction(array $params)
    {
        echo __METHOD__, PHP_EOL;

        print_r($params);

        error_log(date('Y-m-d H:i:s ') . __METHOD__ . "\n", 3, 'e:/job.log');
    }

    public function chainAction(array $params)
    {
        $this->console->handle([
            'task'   => 'main',
            'action' => 'main'
        ]);

        $this->console->handle([
            'task'   => 'main',
            'action' => 'test',
            'params' => $params,
        ]);
    }
}
