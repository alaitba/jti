<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AdminNotification extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'message'];

    protected $guarded = [];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
