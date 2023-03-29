<?php

declare(strict_types=1);

namespace App\Proxy\Model;

use App\Model\StockMarket;

class StockMarketProxy extends StockMarket
{
    /**
     * @param array $codes
     */
    public function getByCodes(array $codes)
    {
        return self::query()->where('codes', 'in', $codes)->get();
    }

}
