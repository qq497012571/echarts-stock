<?php

namespace App\Library\StockApiSupport;

use App\Log;
use GuzzleHttp\Client;
use Hyperf\Utils\Codec\Json;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\HandlerStackFactory;
use GuzzleHttp\Cookie\SessionCookieJar;
use Hyperf\Contract\StdoutLoggerInterface;

class XueqiuApi
{

    /**
     * @var HandlerStackFactory
     */
    #[Inject()]
    public $stackFactory;

    /**
     * @var StdoutLoggerInterface
     */
    #[Inject()]
    public $logger;

    /**
     * @var Client
     */
    private $_client;
    private $_option;


    private $_jar;

    public const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36';
    public const USER_TOKEN = 'xq_a_token';


    public function __construct($sessionId, $token = '')
    {
        $stack = $this->stackFactory->create();
        $this->_jar = new SessionCookieJar($sessionId, true);

        $this->_client = new Client([
            'handler' => $stack,
            'timeout' => 5,
            'swoole' => [
                'timeout' => 5,
                'socket_buffer_size' => 1024 * 1024 * 2,
            ],
            'cookies' => $this->_jar,
        ]);

        $this->_option = [
            'headers' => [
                'User-Agent' => self::USER_AGENT,
            ]
        ];


        if (!$this->_jar->getCookieByName(self::USER_TOKEN)) {
            $this->_client->get('https://xueqiu.com/', $this->_option);
        }


        if ($token) {

            $userCookie = new \GuzzleHttp\Cookie\SetCookie([
                'Name' => self::USER_TOKEN,
                'Value' => $token,
                'Domain' => '.xueqiu.com',
                'Path' => '/',
            ]);

            $this->_jar->setcookie($userCookie);
        }
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

        $url = "https://stock.xueqiu.com/v5/stock/chart/kline.json?symbol=$code&begin=$begin&period=$ma&count=-$limit";
        $this->logger->info($url);
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
        $url = "https://stock.xueqiu.com/v5/stock/portfolio/stock/list.json?size=1000&category=1";
        $this->logger->info("获取雪球股票列表: " . $url);
        $response = $this->_client->request('GET', $url, $this->_option);
        $result = Json::decode($response->getBody()->getContents());
        return $result;
    }

    /**
     * 获取最新一条数据详情
     */
    public function quote($symbol)
    {
        $query = [
            'symbol' => $symbol,
            'extend' => 'detail',
            'is_delay_hk' => false,
        ];
        $queryString = http_build_query($query);
        $url = "https://stock.xueqiu.com/v5/stock/batch/quote.json?" . $queryString;
        $this->logger->info("获取雪球股票详情: " . $url);
        $response = $this->_client->request('GET', $url, $this->_option);
        $result = Json::decode($response->getBody()->getContents());
        return $result;
    }

    /**
     * 删除自选股
     */
    public function cancel($symbol)
    {
        $url = "https://stock.xueqiu.com/v5/stock/portfolio/stock/cancel.json";
        $this->_option['form_params'] = [
            'symbols' => $symbol,
        ];
        $response = $this->_client->request('POST', $url, $this->_option);
        $result = Json::decode($response->getBody()->getContents());
        $this->logger->info(sprintf("雪球股票删除: %s %s", $result['error_code'], $result['error_description']));
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
