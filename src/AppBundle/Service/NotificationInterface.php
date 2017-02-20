<?php


namespace AppBundle\Service;


interface NotificationInterface
{
    public function publish($message, $subject);
}