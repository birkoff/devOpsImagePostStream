<?php

namespace AppBundle\Service;

use Aws\Sdk;

class SnsWrapper implements NotificationInterface
{
    /** @var \Aws\Sns\SnsClient  */
    private $sns;

    /** @var  String $topicArn */
    private $topicArn;

    /**
     * SnsWrapper constructor.
     * @internal param $topicArn
     * @param $topicArn
     */
    public function  __construct($topicArn)
    {
        $this->topicArn = $topicArn;

        $sdk = new Sdk([
            'profile'  => 'birkoff',
            'region'   => 'eu-west-1',
            'version'  => 'latest'
        ]);

        $this->sns = $sdk->createSns();
    }

    /**
     * @param $message
     * @param string $subject
     * @return mixed
     * @throws \Exception
     * @internal param $topicArn
     */
    public function publish($message, $subject = '')
    {
         $result = $this->sns->publish([
            'TopicArn' => $this->topicArn,
            'Message' => $message,
            'Subject' => $subject
         ]);

        if(!$result || !isset($result['MessageId'])) {
            throw new \Exception('Error publishing to sns');
        }

        return $result['MessageId'];
    }

}