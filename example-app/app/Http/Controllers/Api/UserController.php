<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorizationRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\VerifyRegistrationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Wotz\VerificationCode\VerificationCode;

use App\Models\User;

class UserController extends Controller
{
    public function registration(RegistrationRequest $request)
    {

        VerificationCode::send($request->email);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['status' => true, 'message' => 'Пользователь прошел базовую регистрацию', 'data' => $user]);
    }

    public function verifyRegistration(VerifyRegistrationRequest $request)
    {
        if(User::where('email', $request->email)->exists()){

            $user = User::where('email', $request->email)->first();

            if(VerificationCode::verify($request->verify_code, $request->email)){
                $user->markEmailAsVerified();

                return response()->json(['status' => true, 'message' => 'Пользователь подтвердил свой аккаунт', 'data' => $user]);
            }

            return response()->json(['status' => true, 'message' => 'Введенный код не относится ни к одному из пользователей'], 404);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Использованная почта не относится ни к одному из пользователей'], 404);
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
