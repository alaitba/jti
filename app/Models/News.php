<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\morphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * @property Collection media
 */
class News extends Model
{
    use SoftDeletes;
    use HasTranslations;

    protected $guarded = [];

    public $translatable = ['title', 'contents'];

    /**
     * A Post may have media.
     *
     * @return morphMany
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'imageable');
    }
}
