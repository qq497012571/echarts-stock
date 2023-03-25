<?php

declare(strict_types=1);

namespace App\Command;

use App\Library\StockApiSupport\XueqiuApi;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

#[Command]
class TestCommand extends HyperfCommand
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct('demo:command');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {
        $xueqiu = new XueqiuApi('jlb', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOjI4MDYyNjAwNjYsImlzcyI6InVjIiwiZXhwIjoxNjgyMTU3NDE1LCJjdG0iOjE2Nzk1ODgwNDIwNjEsImNpZCI6ImQ5ZDBuNEFadXAifQ.pcX0FxGBkJvVwAl_r_QS7ETQgqCCi8kPI-cGNkHHjdOP3QHQPmpkNjYkwtrpcozSoa3tmFG_ATthzh0gUCe4Guwr1py2iwsXwXvDIT598O16uLs0hCX7v-FGa3sJMBVA_TemmAyWsnxX1iJ-NQZYIUR2HFttpl99wiFtIABQMwcvOv7k5i1Gp974ON5TVvIfOBXSyn7AzKLgkd5TVJNb7lMSD5sPUNYyUW-MRqpUWO4ndfvgaBqUKnaK2n5zJOXqfjIwQmduppy7ZSl4Vn9LUpmjD4MOzoxvIDL1ng1paLC7FodVn5L68Qw6M958mUkr6VYS8Sgt2mjVCy7ew-QVHQ', '92c395900bf9ac802a4072b81013daffdb344d99');
        $xueqiu->add('SH600036');
        $this->line('Hello Hyperf!', 'info');
    }
}
