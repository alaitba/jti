<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TradePoint
 * @package App\Models
 */
class TradePoint extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
