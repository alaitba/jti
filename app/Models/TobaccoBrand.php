<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property string brand
 * @property Collection photos
 */
class TobaccoBrand extends Model
{
    protected $guarded = [];

    /**
     * @return MorphMany
     */
    public function photos()
    {
        return $this->morphMany(Media::class, 'imageable');
    }
}
