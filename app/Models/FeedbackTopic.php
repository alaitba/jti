<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * Class FeedbackTopic
 * @property int id
 * @property string title
 * @package App\Models
 */
class FeedbackTopic extends Model
{
    use SoftDeletes;
    use HasTranslations;

    protected $guarded = [];
    public $translatable = ['title'];
}
