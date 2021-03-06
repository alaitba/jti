<?php

namespace App\Models;

use App\Traits\HasMedia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

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
 * @property Collection questions
 * @property Collection partners
 * @property Media photo
 * @property string period
 * @package App\Models
 */
class Quiz extends Model
{
    use HasTranslations, HasMedia, SoftDeletes;

    protected $guarded = [];

    public $casts = [
        'from_date' => 'date',
        'to_date' => 'date'
    ];
    public $translatable = ['title'];

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
        return $this->hasManyThrough(Partner::class, QuizPartner::class, 'quiz_id', 'id', 'id', 'partner_id');
    }

    /**
     * @return HasMany
     */
    public function quiz_results()
    {
        return $this->hasMany(QuizResult::class);
    }

    /**
     * @return HasMany
     */
    public function poll_results()
    {
        return $this->hasMany(PollResult::class);
    }

    /**
     * @return MorphOne
     */
    public function photo()
    {
        return $this->morphOne(Media::class, 'imageable');
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
                ($this->user_list_file && Storage::disk('local')->exists($this->user_list_file)
                    ? '<a href="' . route('admin.quizzes.custom-file', ['id' => $this->id]) . '">Список</a>'
                    : '<span class="text-danger">Список</span>') . '[' . ($this->partners_count ?? 0) . ']'
            );
    }

    /**
     * @param $user
     * @return int
     */
    public function hasSuccess($user)
    {
        if ($this->type == 'quiz')
        {
            return $this->quiz_results()->where('success', 1)->where('partner_id', $user->id)->count();
        }
        return $this->poll_results()->where('partner_id', $user->id)->count();
    }
}
