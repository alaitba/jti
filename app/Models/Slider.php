<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property int id
 * @property string link
 * @property int position
 * @property int active
 * @property Media image
 */
class Slider extends Model
{
    protected $guarded = [];

    /**
     * @return MorphOne
     */
    public function image()
    {
        return $this->morphOne(Media::class, 'imageable');
    }
}
