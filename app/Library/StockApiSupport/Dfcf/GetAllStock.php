<?php

namespace App\Library\StockApiSupport\Dfcf;


/**
 * 获取所有股票详情明细
 */
class GetAllStock
{
    private $_client;

    public function __construct($client)
    {
        $this->_client = $client;

        $map = [
            'name' => 'f14',
            'code' => 'f12',
            'exchange' => 'f13',
            'current' => 'f2',
            'open' => 'f17',
            'high' => 'f15',
            'low' => 'f16',
            'volume' => 'f5',
            'amount' => 'f6',
            'percent' => 'f3',
            'chg' => 'f4',
            'turnover_rate' => 'f8',
            'market_capital' => 'f20',
            'market_flow_capital' => 'f21',
        ];

        $query = [
            'pn' => 1,
            'pz' => 6000,
            'po' => 1,
            'fltt' => 2,
            'fid' => 3,
            'invt' => 2,
            'ut' => 'bd1d9ddb04089700cf9c27f6f7426281',
            'wbp2u' => '|0|0|0|web',
            'fs' => 'm:1',
            'fields' => implode(',', array_values($map)),
        ];

        // $this->_api = 'https://21.push2.eastmoney.com/api/qt/clist/get?pn=1&pz=1&po=1&np=1&ut=bd1d9ddb04089700cf9c27f6f7426281&fltt=2&invt=2&wbp2u=|0|0|0|web&fid=f3&fs=m:0+t:6,m:0+t:80,m:1+t:2,m:1+t:23,m:0+t:81+s:2048&fields=f1,f2,f3,f4,f5,f6,f7,f8,f9,f10,f12,f13,f14,f15,f16,f17,f18,f20,f21,f23,f24,f25,f22,f11,f62,f128,f136,f115,f152&_=1680936194430';

    }
}
