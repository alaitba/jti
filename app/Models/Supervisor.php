<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Supervisor
 * @package App\Models
 */
class Supervisor extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
