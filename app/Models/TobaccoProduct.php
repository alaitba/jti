<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TobaccoProduct
 * @package App\Models
 */
class TobaccoProduct extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
