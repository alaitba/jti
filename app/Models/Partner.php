<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Partner extends Model implements \Illuminate\Contracts\Auth\Authenticatable, JWTSubject
{
    use Authorizable;
    use Authenticatable;
    use SoftDeletes;

    protected $hidden = ['password'];
    protected $guarded = [];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'mobile_phone', 'mobile_phone');
    }

    /**
     * @return array
     */
    public function tradepointsArray()
    {
        $tradepoints = [];
        foreach ($this->contacts as $contact)
        {
            $tradepoints[$contact->tradepoint->account_code] = $contact->tradepoint->only(['account_name', 'street_address', 'city']);
        }
        return $tradepoints;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
