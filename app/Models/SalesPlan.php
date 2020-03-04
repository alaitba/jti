<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Class SalesPlan
 * @property string account_code
 * @property TobaccoBrand tobacco_brand
 * @package App\Models
 */
class SalesPlan extends Model
{
    protected $guarded = [];

    /**
     * @return HasOne
     */
    public function tradepoint()
    {
        return $this->hasOne(TradePoint::class, 'account_code', 'account_code');
    }

    /**
     * @return HasOne
     */
    public function salesplan2()
    {
        return $this->hasOne(SalesPlan2::class, 'account_code', 'account_code');
    }

    /**
     * @return HasOneThrough
     */
    public function tobacco_brand()
    {
        return $this->hasOneThrough(TobaccoBrand::class, SalesPlan2::class, 'account_code', 'brand', 'account_code', 'brand');
    }
}
