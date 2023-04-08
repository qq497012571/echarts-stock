<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property string $code 
 * @property string $period 
 * @property string $open 
 * @property string $close 
 * @property string $high 
 * @property string $low 
 * @property int $volume 
 * @property int $amount 
 * @property int $chg 
 * @property int $percent 
 * @property string $turnover_rate 
 * @property int $timestamp 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class StockKline extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'stock_kline';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'volume' => 'integer', 'amount' => 'integer', 'chg' => 'integer', 'percent' => 'integer', 'timestamp' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
