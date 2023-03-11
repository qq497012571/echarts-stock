<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Service;

use App\Exception\ServiceException;
use App\Model\UserStock;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\HandlerStackFactory;
use GuzzleHttp\Client;
use Hyperf\Context\Context;
use Hyperf\Utils\Codec\Json;
use Hyperf\Guzzle\RetryMiddleware;

class StockService
{
    /**
     * @var HandlerStackFactory
     */
    #[Inject()]
    public $redisFactory;


    public function get($code, $klt = 101, $limit = 9999)
    {
        if (!$code) {
            throw new ServiceException('股票代码错误');
        }

        $secid = $this->getSecid($code);
        $cdn = rand(1, 500);

        $factory = new HandlerStackFactory();
        $stack = $factory->create();

        $retry = make(RetryMiddleware::class, [
            'retries' => 1,
            'delay' => 10,
        ]);

        $stack->push($retry->getMiddleware(), 'retry');

        $client = make(Client::class, [
            'config' => [
                'handler' => $stack,
            ],
        ]);

        $url = "https://$cdn.push2his.eastmoney.com/api/qt/stock/kline/get?secid={$secid}&ut=fa5fd1943c7b386f172d6893dbfba10b&fields1=f1%2Cf2%2Cf3%2Cf4%2Cf5%2Cf6&fields2=f51%2Cf52%2Cf53%2Cf54%2Cf55%2Cf56%2Cf57%2Cf58%2Cf59%2Cf60%2Cf61&klt=$klt&fqt=1&end=20500101&lmt=$limit&_=1678461967540";
        $response = $client->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36',
            ]
        ]);
        $result = Json::decode($response->getBody()->getContents());

        return $result['data'] ?? [];
    }


    public function getSecid($code)
    {
        if ($code == '000001') {
            return '1.000001';
        }

        if (substr($code, 0, 2) == '60') {
            return "1.{$code}";
        }

        if (substr($code, 0, 2) == '00') {
            return "0.{$code}";
        }
    }


    /**
     * 获取当前用户自选的股票列表
     */
    public function list()
    {
        $user = Context::get('user');
        // UserStock::query()->where('user_id', $user)
    }


}
