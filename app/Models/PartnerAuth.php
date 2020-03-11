<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PartnerAuth
 * @property int id
 * @property int partner_id
 * @property string account_code
 * @property Carbon login
 * @property Carbon last_seen
 * @property string os
 * @package App\Models
 */
class PartnerAuth extends Model
{
    protected $guarded = [];
}
