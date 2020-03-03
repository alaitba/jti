<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchaseWeekDay
 * @package App\Models
 */
class PurchaseWeekDay extends Model
{
    protected $guarded = [];
    protected $casts = [
        'weekdays' => 'json'
    ];
}
