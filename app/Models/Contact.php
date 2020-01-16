<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Contact
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

}
