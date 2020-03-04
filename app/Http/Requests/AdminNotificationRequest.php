<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class AdminNotificationRequest
 * @package App\Http\Requests
 */
class AdminNotificationRequest extends FormRequest
{

    use MessagesTrait;

    /**
     * @return mixed
     */
    public function authorize()
    {
        return Auth::guard('admins')->check();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'title.' . config('app.locale') => 'required',
            'message.' . config('app.locale') => 'required',
            'user_list' => 'required_if:type,list|file'
        ];
    }

}
