<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property string $code 
 * @property string $symbol 
 * @property string $name 
 * @property int $status 
 * @property string $exchange 
 * @property string $current 
 * @property string $open 
 * @property string $high 
 * @property string $low 
 * @property string $percent 
 * @property string $chg 
 * @property int $volume 
 * @property int $amount 
 * @property string $turnover_rate 
 * @property int $market_capital 
 * @property int $market_flow_capital 
 * @property int $timestamp 
 * @property int $last_60_datetime 
 * @property int $last_30_datetime 
 * @property int $last_15_datetime 
 * @property int $last_5_datetime 
 * @property int $last_1_datetime 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class StockMarket extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'stock_market';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'volume' => 'integer', 'amount' => 'integer', 'market_capital' => 'integer', 'market_flow_capital' => 'integer', 'timestamp' => 'integer', 'last_60_datetime' => 'integer', 'last_30_datetime' => 'integer', 'last_15_datetime' => 'integer', 'last_5_datetime' => 'integer', 'last_1_datetime' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
