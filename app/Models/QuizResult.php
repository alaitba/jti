<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class QuizResult
 * @property int id
 * @property int quiz_id
 * @property int partner_id
 * @property int amount
 * @property array questions
 * @property bool success
 * @property Carbon created_at
 * @property Carbon updated_at
 * @package App\Models
 */
class QuizResult extends Model
{
    protected $guarded = [];
    protected $casts = [
        'questions' => 'array'
    ];
}
