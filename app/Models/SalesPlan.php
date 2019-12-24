<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPlan extends Model
{
    protected $guarded = [];

    public function tradepoint()
    {
        return $this->hasOne(TradePoint::class, 'account_code', 'account_code');
    }

    public function salesplan2()
    {
        return $this->hasOne(SalesPlan2::class, 'account_code', 'account_code');
    }
}
