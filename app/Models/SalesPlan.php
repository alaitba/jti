<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class SalesPlan
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
}
