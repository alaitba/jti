<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

/**
 * Class Quiz
 * @property int id
 * @property string type
 * @property string title
 * @property Carbon from_date
 * @property Carbon to_date
 * @property int amount
 * @property bool public
 * @property string|null user_list_file
 * @property bool active
 * @package App\Models
 */
class Quiz extends Model
{
    protected $guarded = [];
    public $casts = [
        'from_date' => 'date',
        'to_date' => 'date'
    ];

    /**
     * @return HasMany
     */
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    /**
     * @return HasManyThrough
     */
    public function partners()
    {
        return $this->hasManyThrough(Partner::class, QuizPartner::class);
    }

    /**
     * @return string
     */
    public function getTypeStringAttribute()
    {
        switch ($this->type) {
            case 'quiz':
                return 'Викторина';
            case 'poll':
                return 'Опрос';
        }
        return '---';
    }

    /**
     * @return string
     */
    public function getPeriodAttribute()
    {
        return $this->from_date->format('d.m.Y') . ' - ' . $this->to_date->format('d.m.Y');
    }

    /**
     * @return string
     */
    public function getTargetAttribute()
    {
        return $this->public ? 'Все'
            : (
                $this->user_list_file && Storage::disk('local')->exists($this->user_list_file)
                    ? '<a href="' . route('admin.quizzes.custom-file', ['id' => $this->id]) . '">Список</a>'
                    : '<span class="text-danger">Список</span>'
            );
    }
}
