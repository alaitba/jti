<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Auth;


/**
 * Class WysiwygRequest
 * @package App\Http\Requests
 */
class WysiwygRequest extends FormRequest
{
    use MessagesTrait;

    /**
     *
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
        /**
         * Создание папки в визивиге
         *
         */
        return [
            'name' => 'required'
        ];


    }
}
