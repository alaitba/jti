<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * @property mixed updated_at
 */
class Reward extends Model
{
    use SoftDeletes;
    use HasTranslations;

    protected $guarded = [];

    public $translatable = ['name', 'description'];

    /**
     * @return mixed
     */
    public function getUpdatedAtStringAttribute()
    {
        return $this->updated_at->locale('ru')->ago();
    }

    /**
     * @return string
     */
    public function getNamesAttribute()
    {
        $kz = $this->getTranslation('name', 'kz');
        return $this->getTranslation('name', 'ru') . ' | ' . ($kz ? $kz : '<span class="text-danger">[нет названия на казахском]</span>');
    }

    /**
     * @return string
     */
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

    /**
     * @return MorphMany
     */
    public function photos()
    {
        return $this->morphMany(Media::class, 'imageable');
    }

}
