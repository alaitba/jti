<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

/**
 * Class QuizQuestion
 * @property int id
 * @property int quiz_id
 * @property string question
 * @property string type
 * @property Collection answers
 * @property Media photo
 * @package App\Models
 */
class QuizQuestion extends Model
{
    use HasTranslations, HasMedia;

    protected $guarded = [];

    public $translatable = ['question'];

    /**
     * @return HasMany
     */
    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    /**
     * @return MorphOne
     */
    public function photo()
    {
        return $this->morphOne(Media::class, 'imageable');
    }
}
