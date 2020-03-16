<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    use HasTranslations;

    protected $guarded = [];

    public $translatable = ['answer'];
}
