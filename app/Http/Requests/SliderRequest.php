<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class SliderRequest
 * @package App\Http\Requests
 */
class SliderRequest extends FormRequest
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
        if ($this->segment(2) == 'create')
        {
            return [
                'image' => 'required|image',
                'link' => 'required|url',
            ];

        }
        return [
            'link' => 'required|url',
        ];
    }

}
