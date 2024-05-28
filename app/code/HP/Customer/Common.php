<?php

namespace HP\Customer;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class Common
{
    const HOST = 'elasticsearch:9200';

    private Client $client;

    public function __construct()
    {

        $this->client = ClientBuilder::create()->setHosts([self::HOST])->build();
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}