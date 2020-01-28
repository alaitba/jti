<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class News extends Model
{
    use SoftDeletes;
    use HasTranslations;

    protected $guarded = [];

    public $translatable = ['title', 'contents'];

    /**
     * A Post may have media.
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'imageable');
    }
}
