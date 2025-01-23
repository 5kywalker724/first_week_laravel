<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('registration', [UserController::class, 'registration'])->middleware('guestUser');
Route::post('registration/verify', [UserController::class, 'verifyRegistration'])->middleware('guestUser');
Route::post('authorization', [UserController::class, 'authorization'])->middleware('guestUser');
Route::post('password/reset/email', [UserController::class, 'passwordResetEmail'])->middleware('guestUser');
Route::post('password/reset/verify', [UserController::class, 'passwordResetVerify'])->middleware('guestUser');
Route::post('password/reset', [UserController::class, 'passwordReset'])->middleware('guestUser');
Route::post('verify/code/resend', [UserController::class, 'verifyCodeResend']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
