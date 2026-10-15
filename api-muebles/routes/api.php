<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MuebleController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\WhatsappConfigController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MediaController;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/muebles', [MuebleController::class, 'index']);
Route::get('/muebles/{slug}', [MuebleController::class, 'show']);
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/whatsapp', [WhatsappConfigController::class, 'index']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/media', [MediaController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('muebles', MuebleController::class);
    Route::apiResource('categorias', CategoriaController::class);
    Route::get('/whatsapp/config', [WhatsappConfigController::class, 'config']);
    Route::put('/whatsapp/config', [WhatsappConfigController::class, 'update']);
});
