<?php
namespace App\Task;

use App\Model\StockMarket;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Guzzle\HandlerStackFactory;
use Hyperf\Contract\StdoutLoggerInterface;
use GuzzleHttp\Client;


/**
 * 更新stock_market表
 */
class StockMaketTask
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    #[Crontab(rule: "*/15 * * * *", memo: "更新stock_market表数据")]
    public function update()
    {
        $factory = new HandlerStackFactory();
        $stack = $factory->create();

        $client = make(Client::class, [
            'config' => [
                'handler' => $stack,
            ],
        ]);

        $map = [
            'name' => 'f14',
            'code' => 'f12',
            'exchange' => 'f13',
            'current' => 'f2',
            'open' => 'f17',
            'high' => 'f15',
            'low' => 'f16',
            'volume' => 'f5',
            'amount' => 'f6',
            'percent' => 'f3',
            'chg' => 'f4',
            'turnover_rate' => 'f8',
            'market_capital' => 'f20',
            'market_flow_capital' => 'f21',
        ];

        $query = [
            'pn' => 1,
            'pz' => 6000,
            'po' => 1,
            'fltt' => 2,
            'fid' => 3,
            'invt' => 2,
            'ut' => 'bd1d9ddb04089700cf9c27f6f7426281',
            'wbp2u' => '|0|0|0|web',
            'fields' => implode(',', array_values($map)),
        ];

        $response = $client->get('https://21.push2.eastmoney.com/api/qt/clist/get?fs=m:0+t:6,m:0+t:80,m:1+t:2,m:1+t:23,m:0+t:81+s:2048&' . http_build_query($query));
        $content = json_decode($response->getBody()->getContents(), true);
        $flipMap = array_flip($map);
        foreach ($content['data']['diff'] as $item) {
            if ($item['f2'] == '-' && $item['f3'] == '-') {
                continue;
            }
            $stockMarket = StockMarket::query()->where('code', $item['f12'])->first();
            if (!$stockMarket) {
                $stockMarket = new StockMarket();
            }
            foreach ($flipMap as $key => $field) {
                $stockMarket->{$field} = $item[$key];
            }

            switch (substr($stockMarket->code, 0, 2)) {
                case '00':
                case '30':
                    $stockMarket->exchange = 'SZ';
                    break;
                case '68':
                case '60':
                    $stockMarket->exchange = 'SH';
                    break;
                default:
                    $stockMarket->exchange = 'BJ';
                    break;
            }

            $stockMarket->symbol = $stockMarket->exchange . $stockMarket->code;
            $stockMarket->save();
        }

        $this->logger->info("更新stock_market表数据");
    }
}