<?php

namespace AppBundle\Service;

use Aws\Sdk;
date_default_timezone_set('UTC');

class AwsElasticCacheService
{
    private $elasticCache;

    public function  __construct()
    {
        $sdk = new Sdk([
            'profile'  => 'birkoff',
            'region'   => 'eu-west-1',
            'version'  => 'latest'
        ]);

        $this->elasticCache = $sdk->createElastiCache();
    }
}