<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Reward extends Model
{
    use SoftDeletes;
    use HasTranslations;

    protected $guarded = [];

    public $translatable = ['name', 'description'];

    public function getUpdatedAtStringAttribute()
    {
        return $this->updated_at->locale('ru')->ago();
    }

    public function getNamesAttribute()
    {
        $kz = $this->getTranslation('name', 'kz');
        return $this->getTranslation('name', 'ru') . ' | ' . ($kz ? $kz : '<span class="text-danger">[нет названия на казахском]</span>');
    }

    public function getHasDescAttribute()
    {
        $ru = $this->getTranslation('description', 'ru');
        $kz = $this->getTranslation('description', 'kz');
        if ($ru != '' && $kz != '')
        {
            return '<i class="la la-check text-success"></i>';
        }
        if ($ru == '' && $kz == '') {
            return '<i class="la la-ban text-danger"></i>';
        }
        return '<i class="la la-check text-warning"></i>';
    }

    public function photos()
    {
        return $this->morphMany(Media::class, 'imageable');
    }

}
