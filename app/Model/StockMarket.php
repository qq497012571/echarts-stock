<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property int $code 
 * @property string $symbol 
 * @property string $name 
 * @property int $status 
 * @property string $exchange 
 * @property string $current 
 * @property string $open 
 * @property string $high 
 * @property string $low 
 * @property int $percent 
 * @property int $chg 
 * @property int $volume 
 * @property int $amount 
 * @property int $market_capital 
 * @property int $timestamp 
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
    protected array $casts = ['id' => 'integer', 'code' => 'integer', 'status' => 'integer', 'percent' => 'integer', 'chg' => 'integer', 'volume' => 'integer', 'amount' => 'integer', 'market_capital' => 'integer', 'timestamp' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
