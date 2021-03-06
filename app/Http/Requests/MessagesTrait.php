<?php

namespace App\Http\Requests;

/**
 * Trait MessagesTrait
 * @package App\Http\Requests
 */
trait MessagesTrait
{
    /**
     * @return array
     */
    public function messages()
    {
        return [
            '*.required' => 'Поле обязательно к заполнению',
            '*.email' => 'Поле должно содержать корректный e-mail',
            '*.integer' => 'Поле должно содержать целое число',
            '*.same' => 'Поле должно быть эквивалентно c предыдущим полем',
            '*.url' => 'Поле должно содержать корректный url',
            '*.unique' => 'Указанное значение уже занято',
            '*.image*' => 'К загрузке допускаются только изображения',

            'query.min' => 'Запрос должен содержать не менее :min символов',
            'query.regex' => 'Запрос содержит недопустимые символы',

            'content.min' => 'Поле должно содержать не менее :min символов',
            'content.regex' => 'Поле содержит недопустимые символы',

            'file.mimetypes' => 'Формат файла не поддерживается',
            'file.max' => 'Файл слишком большой (макс. :max kB)',

            'user_list.required_if' => 'Необходимо загрузить список продавцов',
            'answer.*.*.required_if' => 'Поле обязательно к заполнению',
            'new-answer.*.*.required_if' => 'Поле обязательно к заполнению',
        ];
    }
}
