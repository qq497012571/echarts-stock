<?php
declare(strict_types=1);

namespace App\Process;

use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\SocketIOServer\SocketIO;

#[Process(name: "demo_process")]
class ListenOverlayProcess extends AbstractProcess
{
    public function handle(): void
    {
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $io = $this->container->get(SocketIO::class);
        while (true) {
            // $io->emit('event', '我是demo');
            // $logger->warning('demo_process: emit !!!!');
            sleep(1);
        }
    }
}