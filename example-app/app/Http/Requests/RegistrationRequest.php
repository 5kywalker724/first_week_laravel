<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'name' => 'required|string|max:36|regex:/^[А-ЯЁ][аА-яЯёЁ]+\s+[А-ЯЁ][аА-яЯёЁ]*$/u',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*?:;]/'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле Имя и Фамилия обязательно для заполнения',
            'name.max' => 'Поле Имя и Фамилия вмещает максимум 36 символов',
            'name.regex' => 'Поле Имя и Фамилия должно содержать кириллицу и соответствовать формату: Имя Фамилия',
            'email.required' => 'Поле Электронная почта обязательно для заполнения',
            'email.email' => 'Поле Электронная почта должно содержать валидный адрес эл. почты',
            'email.unique' => 'Введенная эл. почта должна быть уникальной',
            'password.required' => 'Поле Пароль обязательно для заполнения',
            'password.min' => 'Поле Пароль должно быть длинной минимум в 8 символов',
            'password.regex' => 'Поле Пароль должно содержать латинские прописные и строчные буквы, цифры и специальные символы'
        ];
    }
}
