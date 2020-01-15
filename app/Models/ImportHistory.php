<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    protected $table = 'import_history';
    protected $guarded = [];

    public function getCreatedAtStringAttribute()
    {
        return $this->created_at->locale('ru')->ago();
    }
}
