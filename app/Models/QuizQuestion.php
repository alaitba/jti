<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class QuizQuestion
 * @property int id
 * @property int quiz_id
 * @property string question
 * @property string type
 * @package App\Models
 */
class QuizQuestion extends Model
{
    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
