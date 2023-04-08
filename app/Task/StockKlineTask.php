<?php

namespace App\Task;

use App\Model\StockKline;
use App\Model\StockMarket;
use Hyperf\Utils\Parallel;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use App\Library\StockApiSupport\Dfcf;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Utils\Exception\ParallelExecutionException;


/**
 * 更新stock_kline表
 */
class StockKlineTask
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    #[Crontab(rule: "*/5 * * * * *", memo: "更新stock_kline表数据")]
    public function update()
    {
        $parallel = new Parallel(20);
        $stockMarketList = StockMarket::query()->get();
        $dfcf = make(Dfcf::class);
        $client = $dfcf->getClient();

        foreach ($stockMarketList as $stockMarket) {
            $parallel->add(function () use ($stockMarket, $client) {

                if ($stockMarket->exchange == 'SH') {
                    $code = '1.' . $stockMarket->code;
                } else {
                    $code = '0.' . $stockMarket->code;
                }

                $query = [
                    'secid' => $code,
                    'lmt' => 100000,
                    'end' => '20500101',
                    'fqt' => 1,
                    'klt' => 60,
                    'fields1' => 'f1,f2,f3,f4,f5,f6',
                    'fields2' => 'f51,f52,f53,f54,f55,f56,f57,f58,f59,f60,f61',
                ];

                // if ($stockMarket->last_datetime) {
                //     $query['beg'] = $stockMarket->last_datetime;
                // }

                $response = $client->get('/api/qt/stock/kline/get?' . http_build_query($query), [
                    'timeout' => 10,
                    'headers' => [
                        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
                    ]
                ]);

                $content = json_decode($response->getBody()->getContents(), true);

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
                $batchInsert = [];
                foreach ($content['data']['klines'] as $item) {
                    list($datetime, $open, $close, $high, $low, $volume, $amount, $_, $percent, $chg, $turnover_rate) = explode(',', $item);
                    $stockKline = StockKline::query()->where('code', $content['data']['code'])->where('timestamp', strtotime($datetime) * 1000)->first();
                    if (!$stockKline) {
                        $batchInsert[] = [
                            'code' => $content['data']['code'],
                            'period' => $period[101],
                            'timestamp' => strtotime($datetime) * 1000,
                            'open' => $open,
                            'close' => $close,
                            'high' => $high,
                            'low' => $low,
                            'volume' => $volume,
                            'amount' => $amount,
                            'percent' => $percent,
                            'chg' => $chg,
                            'turnover_rate' => $turnover_rate,
                        ];

                        if (count($batchInsert) % 200 == 0) {
                            Db::table('stock_kline')->insert($batchInsert);
                            $stockMarket->last_datetime = date('Ymd', $batchInsert[count($batchInsert) - 1]['timestamp'] / 1000);
                            $stockMarket->save();
                            $this->logger->info($stockMarket->last_datetime);
                            $batchInsert = [];
                        }
                    }
                }

                if (count($batchInsert)) {
                    Db::table('stock_kline')->insert($batchInsert);
                    $stockMarket->last_datetime = date('Ymd', $batchInsert[count($batchInsert) - 1]['timestamp'] / 1000);
                    $stockMarket->save();
                    $this->logger->info($stockMarket->last_datetime);
                }

                return true;
            });
        }

        try {
            $results = $parallel->wait();
            $this->logger->info('更新stock_kline表数据成功啦');
        } catch (ParallelExecutionException $e) {
            var_dump($e->getThrowables());
            $this->logger->warning('更新stock_kline表数据 出错啦');
        }
    }
}
