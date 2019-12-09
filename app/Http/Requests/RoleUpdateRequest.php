<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RoleUpdateRequest extends FormRequest
{
    use MessagesTrait;

    public function authorize()
    {
        return Auth::guard('admins')->check();
    }

    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
