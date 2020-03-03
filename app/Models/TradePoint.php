<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TradePoint
 * @package App\Models
 */
class TradePoint extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function trade_agent()
    {
        return $this->belongsTo(TradeAgent::class, 'employee_code', 'employee_code');
    }
}
