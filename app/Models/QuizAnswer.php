<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    protected $guarded = [];
}
