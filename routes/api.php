<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\HandleExpiredToken;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh-token', [AuthController::class, 'refresh']);

Route::middleware(HandleExpiredToken::class)->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/checkToken', [AuthController::class, 'checkToken']);
    Route::get('/dataUser', [UserController::class, 'dataUser']);
    Route::get('/createUser', [UserController::class, 'createUser']);
    Route::get('/deleteUser/{id}', [UserController::class, 'deleteUser']);
});
