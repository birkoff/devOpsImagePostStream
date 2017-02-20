<?php

namespace AppBundle\Service;

class EventService
{
    private $client;

    function __construct(EventInterface $client)
    {
        $this->client = $client;
    }

    public function sendMessage($message)
    {
        $this->client->sendMessage($message);
    }
}