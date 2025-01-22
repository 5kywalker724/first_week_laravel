<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class UserController extends Controller
{
    public function registration(Request $request){

        $validator = Validator::make($request->all(),[
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
        ], [
            'name.required' => 'Поле Имя и Фамилия обязательно для заполнения',
            'name.max' => 'Поле Имя и Фамилия вмещает максимум 36 символов',
            'name.regex' => 'Поле Имя и Фамилия должно содержать кириллицу и соответствовать формату: Имя Фамилия',
            'email.required' => 'Поле Электронная почта обязательно для заполнения',
            'email.email' => 'Поле Электронная почта должно содержать валидный адрес эл. почты',
            'email.unique' => 'Введенная эл. почта должна быть уникальной',
            'password.required' => 'Поле Пароль обязательно для заполнения',
            'password.min' => 'Поле Пароль должно быть длинной минимум в 8 символов',
            'password.regex' => 'Поле Пароль должно содержать латинские прописные и строчные буквы, цифры и специальные символы'
        ]);

        if($validator->fails()){
            return response()->json(['status' => false, 'message' => 'Ошибка при регистрации пользователя', 'errors' => $validator->errors(), 'error' => $input['name']], 422);
        }

        $verify_code = mt_rand(100000, 999999);

        while(User::where('verify_code', $verify_code)->exists()){
            $verify_code = mt_rand(100000, 999999);
        }

        // наверное тут кусок кода который отвечает за отправку самого кода на почту

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verify_code' => Hash::make($verify_code)
        ]);

        return response()->json(['status' => true, 'message' => 'Пользователь прошел базовую регистрацию', 'data' => $user]);
    }
}
