<?php

include 'classes/Job.php';

use Aws\Sqs\SqsClient;

class AmazonSqsJob extends Job
{
    protected $client;
    protected $queueUrl;

    public function run($argv = [])
    {
        $N = 0;

        while (($result = $this->getMessages())) {
            $entries = [];

            foreach ($result['Messages'] as $message) {
                $this->saveMessage($message);

                $messageId = $message['MessageId'];
                $receiptHandle = $message['ReceiptHandle'];

                $entries[] = [ 'Id' => $messageId, 'ReceiptHandle' => $receiptHandle ];
            }

            $this->deleteMessages($entries);

            $N += 10; echo "$N\r";

            usleep(10000);
        }
    }

    protected function getMessages()
    {
        $this->getClient();

        // Receive a message from the queue
        $result = $this->client->receiveMessage([
            'QueueUrl' => $this->queueUrl,
            'MaxNumberOfMessages' => 10
        ]);

        if ($result['Messages'] == null) {
            return false; // No message to process
        }

        return $result;
    }

    protected function deleteMessages($entries)
    {
        $result = $this->client->deleteMessageBatch([
            'QueueUrl' => $this->queueUrl,
            'Entries'  => $entries
        ]);

        return $result;
    }

    protected function saveMessage($message)
    {
        $messageId = $message['MessageId'];
        $md5Body = $message['MD5OfBody'];
        $receiptHandle = $message['ReceiptHandle'];
        $body = $message['Body'];

        try {
            $this->db->insertAsDict('mws_notification_msg', [
                'message_id'     => $messageId,
                'md5body'        => $md5Body,
                'receipt_handle' => $receiptHandle
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }

        $insertId = $this->db->lastInsertId();

        try {
            $this->db->insertAsDict('mws_notification_msg_body', [
                'msg_id' => $insertId,
                'body'   => $body
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    protected function getClient()
    {
        if ($this->client) {
            return $this->client;
        }

        $credentials = array(
            'region' => 'us-west-2',
            'version' => 'latest',
            'credentials' => array(
                'key'    => 'AKIAJYFJVO2JCCRBJWEA',
                'secret' => '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',
            )
        );

        $this->client = new SqsClient($credentials);

        // Get the queue URL from the queue name.
        $result = $this->client->getQueueUrl(array('QueueName' => "sqs-bte-mws-notification"));
        $this->queueUrl = $result->get('QueueUrl');
    }

    protected function log($var)
    {
        error_log(print_r($var, true), 3, 'sqs.log');
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonSqsJob();
$job->run($argv);
