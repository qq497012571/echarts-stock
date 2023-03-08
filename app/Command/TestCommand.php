<?php

declare(strict_types=1);

namespace App\Command;

use App\Library\Util\Guzzle;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;


#[Command]
class TestCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('demo:command');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {
        // $client = Guzzle::create(['base_uri' => 'https://49.push2his.eastmoney.com']);
        // $response = $client->get('https://49.push2his.eastmoney.com/api/qt/stock/kline/get?secid=0.300964&ut=fa5fd1943c7b386f172d6893dbfba10b&fields1=f1%2Cf2%2Cf3%2Cf4%2Cf5%2Cf6&fields2=f51%2Cf52%2Cf53%2Cf54%2Cf55%2Cf56%2Cf57%2Cf58%2Cf59%2Cf60%2Cf61&klt=101&fqt=1&beg=0&end=20500101&smplmt=460&lmt=1000000&_=1678267423376');


        co(function() {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://httpbin.org/get');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            var_dump($result);
        });

        
        // $requests = function ($total) {
        //     $uri = 'http://127.0.0.1:8126/guzzle-server/perf';
        //     for ($i = 0; $i < $total; $i++) {
        //         yield new Request('GET', $uri);
        //     }
        // };
        
        // $pool = new Pool($client, $requests(100), [
        //     'concurrency' => 5,
        //     'fulfilled' => function (Response $response, $index) {
        //         // this is delivered each successful response
        //     },
        //     'rejected' => function (RequestException $reason, $index) {
        //         // this is delivered each failed request
        //     },
        // ]);
        
        // // Initiate the transfers and create a promise
        // $promise = $pool->promise();
        
        // // Force the pool of requests to complete.
        // $promise->wait();


        $this->line('Hello Hyperf!', 'info');
    }
}
