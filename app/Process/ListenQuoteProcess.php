<?php

declare(strict_types=1);

namespace App\Process;

use App\Model\UserStock;
use App\Model\StockMarket;
use App\Library\Utils\ArrayHelper;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use App\Library\StockApiSupport\XueqiuApi;
use Hyperf\Contract\StdoutLoggerInterface;


/**
 * 更新股票行情
 */
#[Process(name: "listen_quote_process")]
class ListenQuoteProcess extends AbstractProcess
{
    public function handle(): void
    {
        $cookie = 'device_id=d0c2ab3e20f0efab89b9b2a3d2bd20d1;acw_tc=2760826d16797308959006754e12a28c039f94ad04412ac8100785f53aac6b;remember=;xq_a_token=92c395900bf9ac802a4072b81013daffdb344d99;xqat=92c395900bf9ac802a4072b81013daffdb344d99;xq_id_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOjI4MDYyNjAwNjYsImlzcyI6InVjIiwiZXhwIjoxNjgyMTU3NDE1LCJjdG0iOjE2Nzk3MzA5MTA0OTksImNpZCI6ImQ5ZDBuNEFadXAifQ.pkTF1BFC7dTwWug-pxgdKvG3IfuMSwEIWgpjpUcb7Zj9WhQOVTHRkRSIxugdpm0f468hmC4SUfTD9yzKzQuHKDh8DgwDYS-TO7rYlV1C9lKaS-K3rRCNKMrSK7jTX9wilw1au8Gcp7Z9CbOxZ-A-8nsSIjfrjd9Q8mKtFV20ZpqLCjrA3oV7LeF6XQ5zSfi9yjXAhgxQLtupRBPuWfvQ2NkSVo03IML9ZrMoBGKsfvfosnPuH2zBlSB1Hp1az_6ETSYBfOrYPoN_CZAxiBSJqDZ56piMZ2STfQie7l-3M6dNbA6ETLnC4cw3gjsgUTHL9uS0fv_ozsN4H9l_Tvn0zA;xq_r_token=37668350010e6b3c76b4591feaf9041fc62155fc;xq_is_login=1;u=2806260066';
        $xueqiu = new XueqiuApi('listen_quote_process', $cookie);

        $codes = ArrayHelper::array_column(UserStock::query()->groupBy('code')->get('code'), 'code');
        $logger = $this->container->get(StdoutLoggerInterface::class);
        
        while (true) {
            $quoteDetail = $xueqiu->quote(implode(',', $codes));
            if(!isset($quoteDetail['data']['items']) || empty($quoteDetail['data']['items'])) {
                $logger->warning('listen_quote_process error!!!');
            }

            foreach ($quoteDetail['data']['items'] as $item) {
                $market = $item['market'];
                $quote = $item['quote'];

                $market = StockMarket::query()->where('symbol', $quote['symbol'])->first();
                if (!$market) {
                    $market = new StockMarket();
                }
                $market->code = $quote['code'];
                $market->symbol = $quote['symbol'];
                $market->name = $quote['name'];
                $market->status = $quote['status'];
                $market->exchange = $quote['exchange'];
                $market->current = $quote['current'];
                $market->open = $quote['open'];
                $market->high = $quote['high'];
                $market->low = $quote['low'];
                $market->percent = $quote['percent'];
                $market->chg = $quote['chg'];
                $market->volume = $quote['volume'];
                $market->amount = $quote['amount'];
                $market->market_capital = $quote['market_capital'] ?? 0;
                $market->timestamp = $quote['timestamp'];
                $market->save();
            }
            sleep(5);
        }
    }
}
