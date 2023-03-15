<?php

namespace App\Library\StockApiSupport;

use GuzzleHttp\Client;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\HandlerStackFactory;
use Hyperf\Utils\Codec\Json;

class XueqiuApi
{

    /**
     * @var HandlerStackFactory
     */
    #[Inject()]
    public $stackFactory;

    /**
     * 雪球登录后的cookie
     */
    public $userCookie = '';

    /**
     * @var Client
     */
    private $_client;
    private $_option;



    public function __construct($userCookie)
    {
        $this->userCookie = $userCookie;
        $stack = $this->stackFactory->create();

        $this->_client = new Client([
            'base_uri' => "https://stock.xueqiu.com",
            'handler' => $stack,
            'timeout' => 5,
            'swoole' => [
                'timeout' => 10,
                'socket_buffer_size' => 1024 * 1024 * 2,
            ],
        ]);

        $this->_option = [
            'headers' => [
                'authority' => 'stock.xueqiu.com',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36',
                'cookie' => $this->userCookie,
            ]
        ];
    }

    public function handleQueryParams($code, $ma, $limit)
    {
        return [$code, $ma, $limit];
    }

    public function getKline($code, $ma, $limit, $begin = null)
    {   
        if (!$begin) {
            $begin = time() . '000';
        }

        $url = "/v5/stock/chart/kline.json?symbol=$code&begin=$begin&period=$ma&count=-$limit";
        $response = $this->_client->request('GET', $url, $this->_option);
        $result = $response->getBody()->getContents();
        $data = Json::decode($result);
        return $this->handleData($data);
    }

    /**
     * 获取用户自选列表
     */
    public function getList()
    {
        $url = "/v5/stock/portfolio/stock/list.json?size=1000&category=1";
        $response = $this->_client->request('GET', $url, $this->_option);
        $result = Json::decode($response->getBody()->getContents());
        return $result;
    }

    private function handleData($data)
    {
        if (!isset($data['data']['column']) || !isset($data['data']['item'])) {
            return [];
        }

        $column = $data['data']['column'];

        $result = [];
        foreach ($data['data']['item'] as &$v) {
            $result[] = array_combine($column, $v);
        }

        return $result;
    }
}
