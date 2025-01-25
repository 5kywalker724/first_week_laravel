<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class GuestUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if($request->header('Authorization')){
            if (Auth::guard('api')->check()) {
                return response()->json(['status' => false, 'message' =>
                    'Для использования данного функционала пользователь не должен быть аутентифицированным'], 403);
            } else {
                return response()->json(['status' => false, 'message' => 'Невалидный токен'], 401);
            }
        }
        return $next($request);
    }
}
