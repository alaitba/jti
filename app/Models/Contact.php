<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Contact
 * @property TradePoint tradepoint
 * @package App\Models
 */
class Contact extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * @return HasOne
     */
    public function tradepointContact()
    {
        return $this->hasOne(TradePointContact::class, 'contact_code');
    }

    /**
     * @return HasOneThrough
     */
    public function tradepoint()
    {
        return $this->hasOneThrough(
            TradePoint::class,
            TradePointContact::class,
            'contact_code',
            'account_code',
            'contact_code',
            'account_code'
        );
    }

    /**
     * @return BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'mobile_phone', 'mobile_phone');
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->middle_name, $this->last_name]);
    }
}
