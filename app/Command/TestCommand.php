<?php

declare(strict_types=1);

namespace App\Command;

use GuzzleHttp\Client;
use App\Model\StockMark;
use App\Model\StockKline;
use App\Model\StockMarket;
use Hyperf\Utils\Parallel;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Coroutine;
use App\Library\StockApiSupport\Dfcf;
use Psr\Container\ContainerInterface;
use Hyperf\Command\Annotation\Command;
use Hyperf\Guzzle\HandlerStackFactory;
use GuzzleHttp\Cookie\SessionCookieJar;
use App\Service\Queue\UpdateKlineService;
use App\Task\StockKlineTask;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Utils\Exception\ParallelExecutionException;

#[Command]
class TestCommand extends HyperfCommand
{

    public $container;
    public $client;
    public $_option;

    public const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36';



    public function __construct(ContainerInterface $container)
    {
        parent::__construct('demo:command');
        $this->container = $container;
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {

        

        $stockKline = make(StockKlineTask::class);
        $stockKline->update();
        

        // for ($i = 0; $i < 10; $i++) {
        //     $response = $client->get('https://87.push2his.eastmoney.com/api/qt/stock/kline/get?secid=0.002941&ut=fa5fd1943c7b386f172d6893dbfba10b&fields1=f1%2Cf2%2Cf3%2Cf4%2Cf5%2Cf6&fields2=f51%2Cf52%2Cf53%2Cf54%2Cf55%2Cf56%2Cf57%2Cf58%2Cf59%2Cf60%2Cf61&klt=5&fqt=1&beg=20230101&lmt=1000000&_=1680698306382](https://87.push2his.eastmoney.com/api/qt/stock/kline/get?secid=0.002941&ut=fa5fd1943c7b386f172d6893dbfba10b&fields1=f1%2Cf2%2Cf3%2Cf4%2Cf5%2Cf6&fields2=f51%2Cf52%2Cf53%2Cf54%2Cf55%2Cf56%2Cf57%2Cf58%2Cf59%2Cf60%2Cf61&klt=5&fqt=1&beg=20230101&lmt=1000000&_=1680698306382');
        //     $content = $response->getBody()->getContents();
        //     echo $i,"\n";
        // }


        //     $response = $client->get('https://87.push2his.eastmoney.com/api/qt/stock/kline/get?secid=0.002941&ut=fa5fd1943c7b386f172d6893dbfba10b&fields1=f1%2Cf2%2Cf3%2Cf4%2Cf5%2Cf6&fields2=f51%2Cf52%2Cf53%2Cf54%2Cf55%2Cf56%2Cf57%2Cf58%2Cf59%2Cf60%2Cf61&klt=5&fqt=1&beg=20230101&lmt=1000000&_=1680698306382](https://87.push2his.eastmoney.com/api/qt/stock/kline/get?secid=0.002941&ut=fa5fd1943c7b386f172d6893dbfba10b&fields1=f1%2Cf2%2Cf3%2Cf4%2Cf5%2Cf6&fields2=f51%2Cf52%2Cf53%2Cf54%2Cf55%2Cf56%2Cf57%2Cf58%2Cf59%2Cf60%2Cf61&klt=5&fqt=1&beg=20230101&lmt=1000000&_=1680698306382');
        //     $content = $response->getBody()->getContents();
        //     echo 1, "\n";
        // }

        $this->line('Hello Hyperf!', 'info');
    }

    private function task($code, $klt = 101)
    {
        $query = [
            'secid' => $code,
            'lmt' => 100000,
            'end' => '20500101',
            'fqt' => 1,
            'klt' => $klt,
            'fields1' => 'f1,f2,f3,f4,f5,f6',
            'fields2' => 'f51,f52,f53,f54,f55,f56,f57,f58,f59,f60,f61',
        ];

        $period = [
            101 => 'day',
            102 => 'week',
            103 => 'month',
            60 => '60m',
            30 => '30m',
            15 => '15m',
            5 => '5m',
            1 => '1m',
        ];

        $response = $this->client->get('/api/qt/stock/kline/get?' . http_build_query($query), $this->_option);
        $content = json_decode($response->getBody()->getContents(), true);

        // $updateKlineService = make(UpdateKlineService::class);
        // $updateKlineService->push(['content' => $content]);

        // $batchInsert = [];
        // foreach ($content['data']['klines'] as $item) {
        //     list($datetime, $open, $close, $high, $low, $volume, $amount, $_, $percent, $chg, $turnover_rate) = explode(',', $item);
        //     $stockKline = StockKline::query()->where('code', $content['data']['code'])->where('timestamp', strtotime($datetime) * 1000)->first();
        //     if (!$stockKline) {
        //         $batchInsert[] = [
        //             'code' => $content['data']['code'],
        //             'period' => $period[$query['klt']],
        //             'timestamp' => strtotime($datetime) * 1000,
        //             'open' => $open,
        //             'close' => $close,
        //             'high' => $high,
        //             'low' => $low,
        //             'volume' => $volume,
        //             'amount' => $amount,
        //             'percent' => $percent,
        //             'chg' => $chg,
        //             'turnover_rate' => $turnover_rate,
        //         ];

        //         // if (count($batchInsert) % 200 == 0) {
        //         //     Db::table('stock_kline')->insert($batchInsert);
        //         //     $batchInsert = [];
        //         // }
        //     }
        // }

        // if (count($batchInsert)) {
        //     Db::table('stock_kline')->insert($batchInsert);
        // }

        echo "{$content['data']['code']}\n";
        return $content['data']['code'];
    }
}
