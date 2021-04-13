<?php

namespace larkit\oss;

class Base
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}