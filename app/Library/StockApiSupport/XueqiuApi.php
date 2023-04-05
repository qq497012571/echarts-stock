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
    private $_client_option;
    private $_option;

    private $_jar;

    public const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36';


    public function __construct($sessionId, $cookie = '')
    {

        $this->_client_option = [
            'handler' => $this->stackFactory->create(),
            'timeout' => 5,
            'swoole' => [
                'timeout' => 5,
                'socket_buffer_size' => 1024 * 1024 * 2,
            ],
        ];

        if (!$cookie) {
            $this->_jar = new SessionCookieJar($sessionId, true);
            $this->_client_option['cookies'] = $this->_jar;
            $this->_option = [
                'headers' => [
                    'timeout' => 1.5,
                    'user-agent' => self::USER_AGENT,
                ]
            ];
        } else {
            $this->_option = [
                'timeout' => 1.5,
                'headers' => [
                    'user-agent' => self::USER_AGENT,
                    'cookie' => $cookie,
                ]
            ];
        }


        $this->_client = new Client($this->_client_option);

        if (!$cookie) {
            // 伪造设备id device_id
            $cookie = new \GuzzleHttp\Cookie\SetCookie([
                'Name' => 'device_id',
                'Value' => md5($sessionId),
                'Domain' => '.xueqiu.com',
                'Path' => '/',
            ]);
            $this->_jar->setcookie($cookie);

            // 请求一下, 保存session
            $this->_client->get('https://xueqiu.com/', $this->_option);
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
     * 新增自选股
     */
    public function add($symbol)
    {
        $url = "https://stock.xueqiu.com/v5/stock/portfolio/stock/add.json";
        $this->_option['form_params'] = [
            'symbols' => $symbol,
            'category' => 1,
        ];
        $response = $this->_client->request('POST', $url, $this->_option);
        $result = Json::decode($response->getBody()->getContents());
        $this->logger->info("雪球股票-新增: " . json_encode($result));
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
        $this->logger->info("雪球股票删除: " . json_encode($result));
        return $result;
    }

    /**
     * 创建登录二维码
     */
    public function generateQrCode()
    {
        $this->logger->info('generateQrCode cookie: ' . json_encode($this->_jar->toArray()));
        $url = "https://xueqiu.com/snb/provider/generate-qr-code";
        $response = $this->_client->request('POST', $url, $this->_option);
        $result = Json::decode($response->getBody()->getContents());
        $this->logger->info("雪球登录二维码: " . json_encode($result));
        return $result;
    }

    /**
     * serach搜索
     */
    public function search($code, $page = 1, $size = 10)
    {
        $this->logger->info('search cookie: ' . json_encode($this->_jar->toArray()));
        $url = "https://xueqiu.com/query/v1/search/stock.json?code=$code&size=$size&page=$page";
        $this->logger->info('search url: ' . $url);
        
        $response = $this->_client->request('GET', $url, $this->_option);
        $result = Json::decode($response->getBody()->getContents());
        $this->logger->info("雪球搜索: " . json_encode($result));
        return $result;
    }


    public function cookieToString()
    {
        $result = [];
        foreach ($this->_jar->toArray() as $cookie) {
            $result[] = $cookie['Name'] . "=" . $cookie['Value'];
        }
        return implode(';', $result);
    }

    /**
     * 创建登录检查二维码登录状态
     */
    public function queryQrCodeState($code)
    {
        $url = "https://xueqiu.com/snb/provider/query-qr-code-state?code=$code";
        $response = $this->_client->request('GET', $url, $this->_option);
        $result = Json::decode($response->getBody()->getContents());
        $this->logger->info("雪球登录二维码状态: " . json_encode($result));
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
