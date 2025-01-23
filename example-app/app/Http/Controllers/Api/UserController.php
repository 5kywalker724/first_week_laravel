<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorizationRequest;
use App\Http\Requests\PasswordResetEmailRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\PasswordResetVerifyRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\VerifyRegistrationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Wotz\VerificationCode\VerificationCode;

use App\Models\User;
use Wotz\VerificationCode\Models\VerificationCode as VerifyModel;

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

        return response()->json(['status' => true, 'message' => 'Пользователь прошел базовую регистрацию', 'email' => $user->email]);
    }

    public function verifyRegistration(VerifyRegistrationRequest $request)
    {
        if(User::where('email', $request->email)->exists()){

            $user = User::where('email', $request->email)->first();

            if(VerificationCode::verify($request->verify_code, $request->email)){
                $user->markEmailAsVerified();

                return response()->json(['status' => true, 'message' => 'Пользователь подтвердил свой аккаунт', 'data' => $user]);
            }

            return response()->json(['status' => false, 'message' => 'Введенный код не относится ни к одному из пользователей'], 404);
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

        return response()->json(['status' => true, 'message' => 'Пользователь успешно авторизирован', 'user' => $user, 'token' => $token]);
    }

    public function passwordResetEmail(PasswordResetEmailRequest $request)
    {
        if(User::where('email', $request->email)->exists()){
            $user = User::where('email', $request->email)->first();

            VerificationCode::send($user->email);

            return response()->json(['status' => true, 'message' => 'Код для восстановления пароля был отправлен на почту', 'email' => $user->email]);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Введенная почта не относится ни к одному из пользователей'], 404);
        }
    }

    public function passwordResetVerify(PasswordResetVerifyRequest $request)
    {
        if(User::where('email', $request->email)->exists()){

            $user = User::where('email', $request->email)->first();

            if(VerificationCode::verify($request->verify_code, $request->email, false)){
                return response()->json(['status' => true, 'message' => 'Введенный код для восстановления пароля подтвержден',
                    'email' => $user->email, 'verify_code' => $request->verify_code]);
            }

            return response()->json(['status' => false, 'message' => 'Введенный код не относится ни к одному из пользователей'], 404);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Использованная почта не относится ни к одному из пользователей'], 404);
        }
    }

    public function passwordReset(PasswordResetRequest $request)
    {
        if(User::where('email', $request->email)->exists()){

            $user = User::where('email', $request->email)->first();

            if(VerificationCode::verify($request->verify_code, $request->email)){
                $user->update([
                    'password' => Hash::make($request->password)
                ]);

                return response()->json(['status' => true, 'message' => 'Пароль был успешно изменен', 'data' => $user]);
            }

            return response()->json(['status' => false, 'message' => 'Использованный код не относится ни к одному из пользователей']);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Использованная почта не относится ни к одному из пользователей'], 404);
        }
    }

    public function verifyCodeResend(PasswordResetEmailRequest $request)
    {
        if(User::where('email', $request->email)->exists()){
            $codes = VerifyModel::where('verifiable', $request->email)->get();

            foreach ($codes as $code){
                $code->delete();
            }

            VerificationCode::send($request->email);

            return response()->json(['status' => true, 'message' => 'Код для восстановления пароля был отправлен на почту',
                'email' => $request->email]);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Использованная почта не относится ни к одному из пользователей'], 404);
        }
    }
}
