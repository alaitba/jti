<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TradeAgent
 * @package App\Models
 */
class TradeAgent extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
