<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed created_at
 */
class ImportHistory extends Model
{
    protected $table = 'import_history';
    protected $guarded = [];

    /**
     * @return mixed
     */
    public function getCreatedAtStringAttribute()
    {
        return $this->created_at->locale('ru')->ago();
    }
}
