<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

class Partner extends Model implements \Illuminate\Contracts\Auth\Authenticatable
{
    use Authorizable;
    use Authenticatable;
    use SoftDeletes;

    protected $hidden = ['password'];
    protected $guarded = [];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'mobile_phone', 'mobile_phone');
    }
}
