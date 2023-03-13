<?php

declare(strict_types=1);

namespace App\ViewHelper;

class StockViewHelper
{
    public static function createStockMaBtn($code, $klt)
    {
        $selectClass = "layui-btn layui-btn-sm";
        $unSelectClass = "layui-btn layui-btn-sm layui-btn-primary";

        $stockBtn = [
            ['text' => '日K', 'href' => "/stock/kline?code=$code&klt=101", "class" => $klt == 101 ? $selectClass : $unSelectClass],
            ['text' => '周K', 'href' => "/stock/kline?code=$code&klt=102", "class" => $klt == 102 ? $selectClass : $unSelectClass],
            ['text' => '60分', 'href' => "/stock/kline?code=$code&klt=60", "class" => $klt == 60 ? $selectClass : $unSelectClass],
            ['text' => '30分', 'href' => "/stock/kline?code=$code&klt=30", "class" => $klt == 30 ? $selectClass : $unSelectClass],
            ['text' => '15分', 'href' => "/stock/kline?code=$code&klt=15", "class" => $klt == 15 ? $selectClass : $unSelectClass],
            ['text' => '5分', 'href' => "/stock/kline?code=$code&klt=5", "class" => $klt == 5 ? $selectClass : $unSelectClass],
            ['text' => '1分', 'href' => "/stock/kline?code=$code&klt=1", "class" => $klt == 1 ? $selectClass : $unSelectClass],
        ];

        return $stockBtn;
    }


    public static function createStockBtn($code, $klt)
    {
        $selectClass = "layui-btn layui-btn-sm";
        $unSelectClass = "layui-btn layui-btn-sm layui-btn-primary";

        $stockBtn = [
            ['text' => '日K', 'href' => "/stock/kline?code=$code&klt=101", "class" => $klt == 101 ? $selectClass : $unSelectClass],
            ['text' => '周K', 'href' => "/stock/kline?code=$code&klt=102", "class" => $klt == 102 ? $selectClass : $unSelectClass],
            ['text' => '60分', 'href' => "/stock/kline?code=$code&klt=60", "class" => $klt == 60 ? $selectClass : $unSelectClass],
            ['text' => '30分', 'href' => "/stock/kline?code=$code&klt=30", "class" => $klt == 30 ? $selectClass : $unSelectClass],
            ['text' => '15分', 'href' => "/stock/kline?code=$code&klt=15", "class" => $klt == 15 ? $selectClass : $unSelectClass],
            ['text' => '5分', 'href' => "/stock/kline?code=$code&klt=5", "class" => $klt == 5 ? $selectClass : $unSelectClass],
            ['text' => '1分', 'href' => "/stock/kline?code=$code&klt=1", "class" => $klt == 1 ? $selectClass : $unSelectClass],
        ];

        return $stockBtn;
    }

}
