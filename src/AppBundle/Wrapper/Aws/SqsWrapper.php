<?php

namespace AppBundle\Wrapper\Aws;
use AppBundle\Service\EventInterface;
use Aws\Sdk;

date_default_timezone_set('UTC');

class SqsWrapper implements EventInterface
{
    /** @var \Aws\Sqs\SqsClient  */
    private $sqs;

    /** @var  String */
    private $queueUrl;

    /**
     * SqsWrapper constructor.
     * @param $queueName
     * @internal param $queueUrl
     */
    public function  __construct($queueName)
    {
        $this->queueUrl = $this->getQueueUrl($queueName);

        $sdk = new Sdk([
            'profile'  => 'birkoff',
            'region'   => 'eu-west-1',
            'version'  => 'latest'
        ]);

        $this->sqs = $sdk->createSqs();
    }

    /**
     * @param $message
     * @return mixed
     * @throws \Exception
     */
    public function sendMessage($message)
    {
        $result = $this->sqs->sendMessage(array(
            'QueueUrl'    => $this->queueUrl,
            'MessageBody' => $message,
        ));

        if(!$result || !isset($result['MessageId'])) {
            throw new \Exception('Error sending message to SQS Queue: ' . $this->queueUrl);
        }

        return $result['MessageId'];
    }

    /**
     * @param $queueName
     * @return \Aws\Result
     */
    private function getQueueUrl($queueName)
    {
        return $this->sqs->getQueueUrl([
            'QueueName' => $queueName
        ]);
    }
}