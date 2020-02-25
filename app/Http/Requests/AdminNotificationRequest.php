<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AdminNotificationRequest extends FormRequest
{

    use MessagesTrait;

    public function authorize()
    {
        return Auth::guard('admins')->check();
    }

    public function rules()
    {
        return [
            'title.' . config('app.locale') => 'required',
            'message.' . config('app.locale') => 'required',
            'user_list' => 'required_if:type,list|file'
        ];
    }

}
