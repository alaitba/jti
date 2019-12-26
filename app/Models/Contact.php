<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function tradepointContact()
    {
        return $this->hasOne(TradePointContact::class, 'contact_code');
    }

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
