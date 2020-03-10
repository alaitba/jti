<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property Collection contacts
 * @property Contact current_contact
 * @property string sms_code
 * @property Carbon sms_code_sent_at
 * @property string mobile_phone
 * @property string onesignal_token
 */
class Partner extends Model implements \Illuminate\Contracts\Auth\Authenticatable, JWTSubject
{
    use Authorizable;
    use Authenticatable;
    use SoftDeletes;
    use Notifiable;

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
     * @return HasMany
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'mobile_phone', 'mobile_phone');
    }

    /**
     * @return HasOne
     */
    public function current_contact()
    {
        return $this->hasOne(Contact::class, 'contact_uid', 'current_uid');
    }

    /**
     * @return array
     */
    public function tradepointsArray()
    {
        $tradepoints = [];
        foreach ($this->contacts as $contact)
        {
            if (!$contact->tradepoint)
            {
                continue;
            }
            $purchaseDays = $this->purchase_weekdays()
                    ->where('tradepoint', $contact->tradepoint->account_code)
                    ->first()
                    ->weekdays ?? [];
            $tradepoints[$contact->tradepoint->account_code] = array_merge($contact->tradepoint->only([
                'account_code',
                'account_name',
                'street_address',
                'city']), [
                    'contact_uid' => $contact->contact_uid,
                    'purchase_days' => $purchaseDays,
                    'lastUpdated' => SalesPlan2::query()->where('account_code', $contact->tradepoint->account_code)->first()->dsd_till_date ?? null
                ]);
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

    /**
     * @return string
     */
    public function routeNotificationForOneSignal()
    {
        return $this->onesignal_token;
    }

    /**
     * @return HasMany
     */
    public function purchase_weekdays()
    {
        return $this->hasMany(PurchaseWeekDay::class);
    }

}
