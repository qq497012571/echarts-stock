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
use App\Model\StockMark;
use App\Model\UserStock;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\HandlerStackFactory;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\Utils\Codec\Json;
use Hyperf\Guzzle\CoroutineHandler;
use Hyperf\Contract\SessionInterface;

class StockService
{
    /**
     * @var HandlerStackFactory
     */
    #[Inject()]
    public $redisFactory;

    /**
     * @var SessionInterface
     */
    #[Inject()]
    public $session;

    public function get($code, $klt = 101, $limit = 9999, $handleResult = true)
    {
        if (!$code) {
            throw new ServiceException('股票代码错误');
        }

        $secid = $this->getSecid($code);
        $cdn = rand(1, 500);
        
        $client = new Client([
            'base_uri' => "https://$cdn.push2his.eastmoney.com",
            'handler' => HandlerStack::create(new CoroutineHandler()),
            'timeout' => 5,
            'swoole' => [
                'timeout' => 10,
                'socket_buffer_size' => 1024 * 1024 * 2,
            ],
        ]);


        $url = "/api/qt/stock/kline/get?secid={$secid}&ut=fa5fd1943c7b386f172d6893dbfba10b&fields1=f1%2Cf2%2Cf3%2Cf4%2Cf5%2Cf6&fields2=f51%2Cf52%2Cf53%2Cf54%2Cf55%2Cf56%2Cf57%2Cf58%2Cf59%2Cf60%2Cf61&klt=$klt&fqt=1&end=20500101&lmt=$limit&_=1678461967540";
        $response = $client->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36',
            ]
        ]);
        $result = Json::decode($response->getBody()->getContents());

        $handleResult && $this->handleResult($result);

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

    public function handleResult(&$result)
    {
        foreach ($result['data']['klines'] as $key => &$v) {
            list($date, $open, $close, $high, $low, $volume, $turnover, $fullrate, $rate, $_, $hand_rate) = explode(',', $v);
            $timestamp = strtotime($date) * 1000;
            $v = compact('date', 'timestamp', 'open', 'close', 'high', 'low', 'volume', 'turnover', 'fullrate', 'rate', 'hand_rate');
        }
    }
    
    public function list($params)
    {
        $page = $params['page'];
        $limit = $params['limit'];
        
        $user = $this->session->get('user');
        $list = UserStock::query()->where('user_id', $user['id'])->offset(($page - 1) * $limit)->limit($limit)->get();
        return $list;
    }


    /**
     * 股票标记
     * @param $code
     * @param $markOption
     */
    public function addMark($code, $markType, $markOption)
    {
        $user = $this->session->get('user');

        $stockMark = new StockMark();
        $stockMark->code = $code;
        $stockMark->user_id = $user['id'];
        $stockMark->mark_type = $markType;
        $stockMark->mark_option = $markOption;
        $stockMark->save();
    }


}
