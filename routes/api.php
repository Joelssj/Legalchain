<?php

use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');



// Ruta para verificar el token de 4 dígitos
Route::post('/verify-code', [AuthController::class, 'verifyCode']);

// Rutas para la verificación de correo (opcional si usas Laravel predeterminado)
Route::middleware('auth:api')->group(function () {
    // Enviar un correo de verificación nuevamente
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Correo de verificación reenviado.']);
    })->name('verification.send');

    // Verificar el correo electrónico con el hash generado
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // Marca el correo como verificado
        return response()->json(['message' => 'Correo electrónico verificado exitosamente.']);
    })->middleware('signed')->name('verification.verify');
});



// use App\Http\Controllers\AuthController;
// use Illuminate\Support\Facades\Route;

// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
