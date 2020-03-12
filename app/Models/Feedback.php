<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Feedback
 * @property int id
 * @property int feedback_opic_id
 * @property string question
 * @property string|null answer
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Partner partner
 * @package App\Models
 */
class Feedback extends Model
{
    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo(FeedbackTopic::class, 'feedback_topic_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function topic_all()
    {
        return $this->belongsTo(FeedbackTopic::class, 'feedback_topic_id', 'id')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
