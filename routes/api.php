<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;



// Rutas de autenticación (NO requieren autenticación)
Route::post('/register', [AuthController::class, 'register']);          
Route::post('/login', [AuthController::class, 'login']);                
Route::post('/verify-code', [AuthController::class, 'verifyCode']);     

// Rutas para la gestión de contactos (SIN autenticación)
Route::post('/contacts', [ContactController::class, 'store']);

// Buscar contacto por nombre
Route::get('/contacts/nombre/{name}', [ContactController::class, 'searchByName']);

// Buscar contacto por folio
Route::get('/contacts/folio/{folio}', [ContactController::class, 'searchByFolio']);

// Buscar contacto por correo
Route::get('/contacts/email/{email}', [ContactController::class, 'searchByEmail']);

// Buscar contacto por teléfono
Route::get('/contacts/phone/{phone}', [ContactController::class, 'searchByPhone']);

Route::get('/contacts/tag/{tag}', [ContactController::class, 'searchByTag']);

// Ver detalles de un contacto específico
Route::get('/contacts/{id}', [ContactController::class, 'show']);

// Actualizar contacto
Route::put('/contacts/{id}', [ContactController::class, 'update']);

// Eliminar contacto
Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);
//historial de campañas
Route::post('campaigns', [CampaignController::class, 'store']);
//crear campaña
Route::post('crear/campaigns', [CampaignController::class, 'create']);
 //para agregar contacto a la campaña
Route::post('campaigns/{campaignId}/add-contacts', [CampaignController::class, 'addContactsToCampaign']);

Route::get('campaigns/{id}', [CampaignController::class, 'show']);

Route::get('/contacts/campaigns/{email}', [ContactController::class, 'campaignHistory']);

Route::get('contacts/campaigns/{contactId}', [ContactController::class, 'showCampaignsHistory']);


// Rutas protegidas por autenticación (REQUIEREN token)
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);  // Cerrar sesión

    // Reenviar el correo de verificación
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Correo de verificación reenviado.']);
    })->name('verification.send');
});

// Verificar el correo electrónico (Firma segura, no requiere autenticación)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // Marca el correo como verificado
    return response()->json(['message' => 'Correo electrónico verificado exitosamente.']);
})->middleware('signed')->name('verification.verify');


