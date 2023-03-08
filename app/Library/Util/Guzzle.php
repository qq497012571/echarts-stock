<?php

namespace App\Library\Util;

use GuzzleHttp\Client;

class Guzzle
{
    /**
     * @param array $config
     * @return Client
     */
    public static function create(array $config)
    {
        return make(Client::class, ['config' => $config]);
    }
}