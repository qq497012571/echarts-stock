<?php

declare(strict_types=1);

namespace App\Process;

use App\Library\StockApiSupport\XueqiuApi;
use App\Library\Utils\ArrayHelper;
use App\Model\StockAlarm;
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
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $io = $this->container->get(SocketIO::class);
        $xueqiu = new XueqiuApi('listen_alarm_process');


        while (true) {
            $alarmList = StockAlarm::query()->where('is_del', 0)->where('status', 0)->where('expire_time', '>', time())->get();
            if (count($alarmList)) {
                $codes = array_unique(ArrayHelper::array_column($alarmList, 'code'));
                $quoteDetail = $xueqiu->quote(implode(',', $codes));
                $quoteMap = [];
                foreach ($quoteDetail['data']['items'] as $item) {
                    $quoteMap[$item['quote']['symbol']] = $item['quote'];
                }

                foreach ($alarmList as $alarm) {
                    $quote = $quoteMap[$alarm->code];
                    if ($alarm->timing_type == 1) {
                        if ($quote['high'] >= $alarm->price) {
                            $alarm->status = 1;
                            $alarm->trigger_time = $quote['timestamp'];
                            $alarm->save();
                            $io->to($alarm->user_id)->emit('alarm', '警报警报!!');
                        }
                    } else {
                        if ($quote['low'] <= $alarm->price) {
                            $alarm->status = 1;
                            $alarm->trigger_time = $quote['timestamp'];
                            $alarm->save();
                            $io->to($alarm->user_id)->emit('alarm', '警报警报!!');
                        }
                    }
                }
            }

            $logger->warning('demo_process: alarm emit !!!!');
            sleep(5);
        }
    }
}
