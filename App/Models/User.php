<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id';
    public $incrementing = false; // Clave primaria no incremental
    protected $keyType = 'string'; // Clave primaria de tipo string

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'verification_code', // Código de verificación
        'verification_code_status', // Estado del código de verificación
        'email_verified_at', // Fecha de verificación del correo
    ];

    /**
     * Atributos ocultos en las respuestas JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code', // Oculta el código de verificación
    ];

    /**
     * Atributos que deben ser casteados.
     */
    protected $casts = [
        'id' => 'string', // Forzar clave primaria como string
        'email_verified_at' => 'datetime', // Convertir a fecha
    ];

    /**
     * Hook para asignar automáticamente un UUID y un código de verificación.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Asignar un UUID si el ID está vacío
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // Generar el código de verificación y asignar estado activo
            $model->verification_code = rand(1000, 9999); // Código numérico de 4 dígitos
            $model->verification_code_status = 'active'; // Establecer estado activo
            $model->email_verified_at = null; // Asegurar que inicia como null
        });
    }

    /**
     * Verifica si el código proporcionado coincide y está activo.
     */
    public function verifyCode($code)
    {
        return $this->verification_code === $code && $this->verification_code_status === 'active';
    }

    /**
     * Verifica si el usuario ya está verificado.
     */
    public function isVerified()
    {
        return !is_null($this->email_verified_at) && $this->verification_code_status === 'inactive';
    }

    /**
     * Personaliza el orden de los atributos en las respuestas JSON.
     */
    public function toArray()
    {
        $array = parent::toArray();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Envía un correo electrónico para verificar el correo del usuario.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }
}

















