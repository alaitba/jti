<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Contracts\NotifyTemplate as NotifyTemplateContract;

class NotifyTemplate extends Model implements NotifyTemplateContract
{
    protected $table = 'notify_templates';

    protected $fillable = [
        'type',
        'name',
        'display_name',
        'data',
        'params',
    ];
}
