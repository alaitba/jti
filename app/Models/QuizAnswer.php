<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

/**
 * Class QuizAnswer
 * @property int id
 * @property int quiz_question_id
 * @property string answer
 * @property bool correct
 * @package App\Models
 */
class QuizAnswer extends Model
{
    use HasTranslations, HasMedia;

    protected $guarded = [];

    public $translatable = ['answer'];

    /**
     * @return MorphOne
     */
    public function photo()
    {
        return $this->morphOne(Media::class, 'imageable');
    }
}
