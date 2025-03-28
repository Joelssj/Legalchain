<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Registro de usuario
     */
    public function register(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Crear el usuario sin crear la suscripción todavía
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Los campos de verificación se manejarán automáticamenteeee
        ]);
    
        // Enviar el código de verificación por correo
        try {
            $verificationCode = $user->verification_code;  // El código generado en el modelo
            Mail::raw("Tu código de verificación es: $verificationCode", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Código de Verificación');
            });
            Log::info('Código de verificación enviado a: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Error al enviar el correo: ' . $e->getMessage());
            return response()->json([
                'message' => 'No se pudo enviar el código de verificación. Por favor, intente nuevamente.',
            ], 500);
        }
    
        return response()->json([
            'message' => 'Usuario registrado exitosamente. Por favor verifica tu correo con el código enviado.',
            'user' => $user->makeHidden(['password', 'verification_code']),
        ], 201);
    }
    


public function verifyCode(Request $request)
{
    // Validación
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email',
        'code' => 'required|string|min:4|max:4',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Buscar al usuario
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'Usuario no encontrado.'], 404);
    }

    // Verificar el código
    if ($user->verifyCode($request->code)) {
        // Actualizar el estado del usuario
        $user->update([
            'email_verified_at' => now(),
            'verification_code_status' => 'inactive',
        ]);

        // Crear la suscripción con plan 'basic'
        $subscription = Subscription::create([
            'user_id' => $user->id,  
            'plan' => 'basic',        // Plan básico por defecto
            'start_date' => now(),  
            'end_date' => null,       // Sin fecha de finalización para el plan 'basic'
        ]);

        return response()->json([
            'message' => 'Correo verificado exitosamente y suscripción creada.',
            'user' => $user->makeHidden(['password', 'verification_code']),
            'subscription' => $subscription,
        ], 200);
    }

    return response()->json(['message' => 'El código de verificación es incorrecto o ya no está activo.'], 400);
}


    
    /**
     * Inicio de sesión del usuario.
     */
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Intentar autenticar al usuario con las credenciales
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }
    
        // Obtener el usuario autenticado
        $user = Auth::user();
    
        // Verificar si el código de verificación está activo
        if ($user->verification_code_status !== 'inactive') {
            return response()->json([
                'message' => 'Debes verificar tu correo electrónico ingresando el código enviado.',
            ], 403);
        }
    
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ]);
    }
}




