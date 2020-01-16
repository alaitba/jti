<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TradePointContact
 * @package App\Models
 */
class TradePointContact extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function tradepoint()
    {
        return $this->belongsTo(TradePoint::class, 'account_code');
    }
}
