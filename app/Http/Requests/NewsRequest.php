<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class NewsRequest
 * @package App\Http\Requests
 */
class NewsRequest extends FormRequest
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
            'contents.' . config('app.locale') => 'required',
        ];
    }

}
