<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class PermissionUpdateRequest
 * @package App\Http\Requests
 */
class PermissionUpdateRequest extends FormRequest
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
            'name' => 'required',
        ];
    }
}
