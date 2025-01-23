<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyRegistrationRequest extends FormRequest
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
            'verify_code' => 'required|integer|max_digits:6'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Электронная почта должна быть передана для запроса',
            'email.email' => 'Электронная почта должна соответствовать формату эл. почт',
            'verify_code.required' => 'Поле с кодом обязательно для заполнения',
            'verify_code.integer' => 'Значение введенное в поле с кодом должно быть числом',
            'verify_code.max_digits' => 'Значение введенное в поле с кодом вмещает максимум 6 чисел'
        ];
    }
}
