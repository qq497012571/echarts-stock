<?php
declare(strict_types=1);

namespace App\Process;

use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Redis\RedisFactory;

/**
 * 监听overlay
 */
#[Process(name: "listen_overlay_process")]
class ListenOverlayProcess extends AbstractProcess
{
    public function handle(): void
    {
        $logger = $this->container->get(StdoutLoggerInterface::class);

        $redis = make(RedisFactory::class)->get('default');

        // $redis->sAdd('demo', 1);
        // $redis->sAdd('demo', 2);
        // $redis->sAdd('demo', 1);

        while (true) {
            // $io = $this->container->get(\Hyperf\SocketIOServer\SocketIO::class);
            // $io->emit('say', 'process say: hello world');
            // $codes = $redis->sMembers('demo');
            // var_dump($codes);
            sleep(1);
        }
    }
}