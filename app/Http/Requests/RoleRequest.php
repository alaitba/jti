<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class RoleRequest
 * @package App\Http\Requests
 */
class RoleRequest extends FormRequest
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
            'name' => 'required|unique:roles',
        ];
    }
}
