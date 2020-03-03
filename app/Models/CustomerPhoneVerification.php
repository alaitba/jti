<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerPhoneVerification
 * @property string sms_code
 * @property Carbon sms_code_sent_at
 * @property string mobile_phone
 * @package App\Models
 */
class CustomerPhoneVerification extends Model
{
    protected $guarded = [];
}
