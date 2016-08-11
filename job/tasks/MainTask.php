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
