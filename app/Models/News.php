<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\morphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

/**
 * @property Collection media
 */
class News extends Model
{
    use SoftDeletes;
    use HasTranslations;

    protected $guarded = [];

    public $casts = [
        'from_date' => 'date',
        'to_date' => 'date'
    ];

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

    /**
     * @return string
     */
    public function getPeriodAttribute()
    {
        $period="Дата еще не указана";
        if ($this->from_date!=null && $this->to_date!=null){
            $period=$this->from_date->format('d.m.Y') . ' - ' . $this->to_date->format('d.m.Y');
        }

        return $period;
    }

    /**
     * @return HasManyThrough
     */
    public function partners()
    {
        return $this->hasManyThrough(Partner::class, NewsPartner::class, 'news_id', 'id', 'id', 'partner_id');
    }

    /**
     * @return string
     */
    public function getTargetAttribute()
    {
        return $this->public ? 'Все'
            : (
                ($this->user_list_file && Storage::disk('local')->exists($this->user_list_file)
                    ? '<a href="' . route('admin.news.custom-file', ['id' => $this->id]) . '">Список</a>'
                    : '<span class="text-danger">Список</span>') . '[' . ($this->partners->count() ?? 0) . ']'
            );
    }
}
