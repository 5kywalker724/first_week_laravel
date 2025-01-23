<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if($request->header('Authorization')){
            return response()->json(['status' => false, 'message' =>
                'Для использования данного функционала пользователь не должен быть аутентифицированным'], 403);
        }
        return $next($request);
    }
}
