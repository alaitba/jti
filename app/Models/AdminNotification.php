<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/**
 * @property int admin_id
 * @property string type
 * @property null|string user_list_file
 */
class AdminNotification extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'message'];

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
