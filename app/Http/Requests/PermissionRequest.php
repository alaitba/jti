<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PermissionRequest extends FormRequest
{
    use MessagesTrait;

    public function authorize()
    {
        return Auth::guard('admins')->check();
    }

    public function rules()
    {
        return [
            'name' => 'unique:permissions|required',
        ];
    }
}
