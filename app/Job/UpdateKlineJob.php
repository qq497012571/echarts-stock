<?php

declare(strict_types=1);

namespace App\Job;

use App\Model\StockKline;
use Hyperf\AsyncQueue\Job;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Hyperf\Contract\StdoutLoggerInterface;


/**
 * 处理k线更新
 */
class UpdateKlineJob extends Job
{
    // #[Inject]
    // public ContainerInterface $container;    

    // #[Inject]
    // private StdoutLoggerInterface $logger;

    public $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function handle()
    {
        $klt = $this->params['klt'];
        $content = json_decode($this->params['content'], true);
        $logger = make(StdoutLoggerInterface::class);

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
                    'period' => $period[$klt],
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
                    $batchInsert = [];
                }
            }
        }

        if (count($batchInsert)) {
            Db::table('stock_kline')->insert($batchInsert);
        }

        $logger->info('UpdateKlineJob');
    }
}