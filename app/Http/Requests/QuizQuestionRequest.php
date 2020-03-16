<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class QuizQuestionRequest extends FormRequest
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
        return [
            'question.' . config('app.locale') => 'required',
            'answer.*.' . config('app.locale') => 'required_if:type,choice',
            'new-answer.*.' . config('app.locale') => 'required_if:type,choice',
        ];
    }
}
