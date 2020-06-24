<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Admin
 * @package App\Models
 */
class Admin extends Model implements \Illuminate\Contracts\Auth\Authenticatable
{
    use Authorizable;
    use Authenticatable;
    use SoftDeletes;
    use HasRoles;
    use Notifiable;

    protected  $guard_name = 'admin';

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'super_user',
        'develop',
    ];

    protected $hidden = [
        'password',
    ];

    protected $filterable = [
        'id',
        'name',
        'email',
        'active',
        'develop',
    ];

    /**
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if (strlen($value))
        {
            $this->attributes['password'] = bcrypt($value);
        }
    }

}
