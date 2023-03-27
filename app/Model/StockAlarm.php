<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property int $user_id 
 * @property string $code 
 * @property string $price 
 * @property int $status 
 * @property int $timing_type 
 * @property string $time_type 
 * @property string $push_channel 
 * @property int $expire_time 
 * @property int $trigger_time 
 * @property string $title 
 * @property string $remark 
 * @property int $is_del 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class StockAlarm extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'stock_alarm';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'status' => 'integer', 'timing_type' => 'integer', 'expire_time' => 'integer', 'trigger_time' => 'integer', 'is_del' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
