<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Registro de un nuevo usuario.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        try {
            $verificationCode = $user->verification_code;
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
    

    /**
     * Verificar el código de verificación.
     */
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'code' => 'required|string|min:4|max:4',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }
    
        // Verificar el código de verificación y su estado
        if ($user->verifyCode($request->code)) {
            $user->update([
                'email_verified_at' => now(), // Actualizar con la fecha y hora actual
                'verification_code_status' => 'inactive', // Marcar el token como inactivo
            ]);
    
            return response()->json([
                'message' => 'Correo verificado exitosamente.',
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
    
        // Retornar la respuesta de inicio de sesión exitoso con el usuario
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
    

    /**
     * Cerrar sesión del usuario.
     */
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ]);
    }
}


















// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Validator;


// class AuthController extends Controller
// {
//     /**
//      * Registro de un nuevo usuario
//      */
//     public function register(Request $request)
//     {
//         // Validar los datos de entrada
//         $validator = Validator::make($request->all(), [
//             'name' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users',
//             'password' => 'required|string|min:8|confirmed',
//         ]);
    
//         if ($validator->fails()) {
//             return response()->json($validator->errors(), 422);
//         }
    
//         // Crear el usuario con el UUID asignado manualmente
//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//         ]);
    
//         return response()->json([
//             'message' => 'Usuario registrado exitosamente',
//             'user' => $user->makeHidden(['password']), // Ocultar la contraseña en la respuesta
//         ], 201);
//     }
    
    

//     /**
//      * Inicio de sesión del usuario
//      */
//     public function login(Request $request)
//     {
//         // Validar los datos de entrada
//         $credentials = $request->validate([
//             'email' => 'required|email',
//             'password' => 'required',
//         ]);

//         // Verificar credenciales
//         if (!Auth::attempt($credentials)) {
//             return response()->json(['message' => 'Credenciales inválidas'], 401);
//         }

//         // Obtener el usuario autenticado
//         $user = Auth::user();

//         return response()->json([
//             'message' => 'Inicio de sesión exitoso',
//             'user' => $user,
//         ]);
//     }

//     /**
//      * Cerrar sesión del usuario
//      */
//     public function logout()
//     {
//         Auth::logout();

//         return response()->json([
//             'message' => 'Sesión cerrada exitosamente',
//         ]);
//     }
// }
