<?php

namespace App\Library\Support;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;

/**
 * 东方财富网下载器
 */
class DfcfDownload
{
    private $host = 'https://%s.push2his.eastmoney.com';

    private $fetchStockApi = '';

    public function getHost()
    {
        return sprintf($this->host, rand(1, 999));
    }

    /**
     * @param [type] $code
     * @return void
     */
    public function fetch($code, $ma)
    {
        run(function() {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://httpbin.org/get');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            var_dump($result);
        });
    }

}