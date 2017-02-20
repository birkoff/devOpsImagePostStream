<?php

namespace AppBundle\Service;

class NotificationService
{
    private $client;

    function __construct(NotificationInterface $client)
    {
        $this->client = $client;
    }

    public function publish($message, $subject)
    {
        return $this->client->publish($message, $subject);
    }
}