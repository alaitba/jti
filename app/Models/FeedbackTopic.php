<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FeedbackTopic
 * @property int id
 * @property string title
 * @package App\Models
 */
class FeedbackTopic extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
