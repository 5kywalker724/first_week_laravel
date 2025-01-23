<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*?:;]/'
            ],
            'verify_code' => 'required|integer|max_digits:6'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Электронная почта должна быть передана для запроса',
            'email.email' => 'Электронная почта должна соответствовать формату эл. почт',
            'password.required' => 'Поле Пароль обязательно для заполнения',
            'password.min' => 'Поле Пароль должно быть длинной минимум в 8 символов',
            'password.regex' => 'Поле Пароль должно содержать латинские прописные и строчные буквы, цифры и специальные символы',
            'verify_code.required' => 'Верификационный код должен быть передан для запроса',
            'verify_code.integer' => 'Верификационный код должен быть числом',
            'verify_code.max_digits' => 'Значение верификационного кода вмещает максимум 6 чисел'
        ];
    }
}
