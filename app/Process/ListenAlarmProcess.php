<?php

declare(strict_types=1);

namespace App\Process;

use App\Library\StockApiSupport\XueqiuApi;
use App\Library\Utils\ArrayHelper;
use App\Model\StockAlarm;
use App\Model\StockMark;
use App\Model\UserStock;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\SocketIOServer\SocketIO;

#[Process(name: "listen_alarm_process")]
class ListenAlarmProcess extends AbstractProcess
{
    public function handle(): void
    {
        
        $xueqiu = new XueqiuApi('listen_alarm_process', 'device_id=d0c2ab3e20f0efab89b9b2a3d2bd20d1;acw_tc=2760826d16797308959006754e12a28c039f94ad04412ac8100785f53aac6b;remember=;xq_a_token=92c395900bf9ac802a4072b81013daffdb344d99;xqat=92c395900bf9ac802a4072b81013daffdb344d99;xq_id_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOjI4MDYyNjAwNjYsImlzcyI6InVjIiwiZXhwIjoxNjgyMTU3NDE1LCJjdG0iOjE2Nzk3MzA5MTA0OTksImNpZCI6ImQ5ZDBuNEFadXAifQ.pkTF1BFC7dTwWug-pxgdKvG3IfuMSwEIWgpjpUcb7Zj9WhQOVTHRkRSIxugdpm0f468hmC4SUfTD9yzKzQuHKDh8DgwDYS-TO7rYlV1C9lKaS-K3rRCNKMrSK7jTX9wilw1au8Gcp7Z9CbOxZ-A-8nsSIjfrjd9Q8mKtFV20ZpqLCjrA3oV7LeF6XQ5zSfi9yjXAhgxQLtupRBPuWfvQ2NkSVo03IML9ZrMoBGKsfvfosnPuH2zBlSB1Hp1az_6ETSYBfOrYPoN_CZAxiBSJqDZ56piMZ2STfQie7l-3M6dNbA6ETLnC4cw3gjsgUTHL9uS0fv_ozsN4H9l_Tvn0zA;xq_r_token=37668350010e6b3c76b4591feaf9041fc62155fc;xq_is_login=1;u=2806260066');


        while (true) {
            $alarmList = StockAlarm::query()->where('is_del', 0)->where('status', 0)->where('expire_time', '>', time())->get()->toArray();
            if (count($alarmList)) {
                $codes = array_unique(ArrayHelper::array_column($alarmList, 'code'));
                $quoteDetail = $xueqiu->quote(implode(',', $codes));
                $quoteMap = [];
                foreach ($quoteDetail['data']['items'] as $item) {
                    $quoteMap[$item['quote']['symbol']] = $item['quote'];
                }
                foreach ($alarmList as $alarm) {
                    $quote = $quoteMap[$alarm['code']];
                    // 升破
                    if ($alarm['timing_type'] == 1 && $quote['high'] >= $alarm['price']) {
                        $this->trigger($alarm['id'], $quote['timestamp']);
                    }

                    // 跌破
                    if ($alarm['timing_type'] == 2 && $quote['low'] <= $alarm['price']) {
                        $this->trigger($alarm['id'], $quote['timestamp']);
                    }
                }
            }
            sleep(5);
        }
    }


    public function trigger($alarmId, $timestamp)
    {
        $alarm = StockAlarm::query()->where('id', $alarmId)->first();
        if (!$alarm) {
            return;
        }

        if ($alarm->auto_destory == 1) {
            $alarm->is_del = 1;
        }
        $alarm->status = 1;
        $alarm->trigger_time = $timestamp;
        $alarm->save();

        if ($alarm->auto_destory == 1) {
            StockMark::query()->where('alarm_id', $alarm->id)->delete();
        }
        
        $io = $this->container->get(SocketIO::class);
        $io->to($alarm->user_id)->emit('alarm', json_encode($alarm->toArray()));
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $logger->info('emit alarm => ' . $alarm->user_id);
    }
}
