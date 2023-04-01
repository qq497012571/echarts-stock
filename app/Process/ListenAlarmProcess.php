<?php

declare(strict_types=1);

namespace App\Process;

use App\Library\StockApiSupport\XueqiuApi;
use App\Library\Utils\ArrayHelper;
use App\Model\StockAlarm;
use App\Model\StockMark;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\SocketIOServer\SocketIO;


/**
 * 监听股票预警线
 */
#[Process(name: "listen_alarm_process")]
class ListenAlarmProcess extends AbstractProcess
{
    public function handle(): void
    {
        $xueqiu = new XueqiuApi('listen_alarm_process');
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
        $alarm->status = 1;
        $alarm->trigger_time = $timestamp;
        $alarm->save();

        StockMark::query()->where('alarm_id', $alarmId)->update(['pause' => 1]);

        $io = $this->container->get(SocketIO::class);
        $io->to($alarm->user_id)->emit('alarm', json_encode(['code' => 0, 'msg' => '', 'data' => $alarm->toArray()]));
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $logger->info('emit alarm => ' . $alarm->user_id);
    }
}
