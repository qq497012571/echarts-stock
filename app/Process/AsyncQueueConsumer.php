<?php

declare(strict_types=1);

namespace App\Process;

use Hyperf\Process\Annotation\Process;
use Hyperf\AsyncQueue\Process\ConsumerProcess;

#[Process(name: "async-queue")]
class AsyncQueueConsumer extends ConsumerProcess
{
    
}