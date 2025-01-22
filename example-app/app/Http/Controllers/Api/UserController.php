<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorizationRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\VerifyRegistrationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class UserController extends Controller
{
    public function registration(RegistrationRequest $request)
    {
        $verify_code = mt_rand(100000, 999999);

        while(User::where('verify_code', $verify_code)->exists()){
            $verify_code = mt_rand(100000, 999999);
        }

        // наверное тут кусок кода который отвечает за отправку самого кода на почту

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verify_code' => $verify_code
        ]);

        return response()->json(['status' => true, 'message' => 'Пользователь прошел базовую регистрацию', 'data' => $user]);
    }

    public function verifyRegistration(VerifyRegistrationRequest $request)
    {
        if(User::where('verify_code', $request->verify_code)->exists()){
            $user = User::where('verify_code', $request->verify_code)->first();

            $user->markEmailAsVerified();

            return response()->json(['status' => true, 'message' => 'Пользователь подтвердил свой аккаунт', 'data' => $user]);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Введенный код не относится ни к одному из пользователей'], 404);
        }
    }

    public function authorization(AuthorizationRequest $request)
    {
        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json(['status' => false, 'message' => 'Введенные данные не относятся к существующему аккаунту'], 401);
        }

        $user = Auth::user();

        if($request->filled('remember_me')) {
            $token = $user->createToken('token', ['*'], now()->addDays(7))->plainTextToken;
        }
        else{
            $token = $user->createToken('token', ['*'], now()->addDays(1))->plainTextToken;
        }

        $cookie = cookie('token', $token);

        return response()->json(['status' => true, 'message' => 'Пользователь успешно авторизирован', 'token' => $token])->withCookie($cookie);
    }
}
