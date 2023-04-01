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
 * 更新行情
 */
#[Process(name: "listen_quote_process")]
class ListenQuoteProcess extends AbstractProcess
{
    public function handle(): void
    {

        return;
        $xueqiu = new XueqiuApi('listen_quote_process');
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
