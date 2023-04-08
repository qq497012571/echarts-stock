<?php

namespace App\Library\StockApiSupport;

use GuzzleHttp\Client;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\HandlerStackFactory;
use Hyperf\Contract\StdoutLoggerInterface;

/**
 * 东方财富
 */
class Dfcf
{
    /**
     * @var StdoutLoggerInterface
     */
    #[Inject()]
    public $logger;

    public function getClient()
    {
        $factory = new HandlerStackFactory();
        $stack = $factory->create(['max_connections' => 20]);

        return make(Client::class, [
            'config' => [
                'base_uri' => sprintf('https://%d.push2his.eastmoney.com', rand(1, 99)),
                'handler' => $stack,
            ],
        ]);
    }

}
