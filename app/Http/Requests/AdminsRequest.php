<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


/**
 * Class AdminsRequest
 * @package App\Http\Requests
 */
class AdminsRequest extends FormRequest
{
    use MessagesTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admins')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        if ($this->segment(2) == 'admins' && $this->segment(4) == 'update') {
            return [
                'name' => 'required',
                'email' => 'required|email|unique:admins,email,' . $this->segment(3),
            ];
        }

        return [
            'name' => 'required',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required'
        ];
    }
}
