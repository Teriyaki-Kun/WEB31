<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\spotController;
use App\Http\Middleware\EnsureRoleHasMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('guest')->group(function(){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    
});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class,'logout']);
    Route::get('spot/{spot}/reviews',[ReviewController::class,'reviews']);
    Route::apiResource('spot',spotController::class);

    Route::apiResource('review',ReviewController::class)
    ->only([
        'store',
        'destroy'
    ])
    ->middlewareFor(['store'], 'ensureUserHasRole:USER')
    ->middlewareFor(['destroy'], 'ensureUserHasRole:ADMIN');
});

Route::get('/user', function(Request $request){
    return $request->user();
})->middleware('auth:sanctum');
