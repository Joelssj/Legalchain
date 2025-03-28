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


    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'verification_code', // Código de verificación
        'verification_code_status', // Estado del código de verificación
        'email_verified_at', 
    ];


    protected $hidden = [
        'password',
        'remember_token',
        'verification_code', // Oculta el código de verificación
    ];


    protected $casts = [
        'id' => 'string', 
        'email_verified_at' => 'datetime', 
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }


            $model->verification_code = rand(1000, 9999);
            $model->verification_code_status = 'active'; 
            $model->email_verified_at = null; 
        });
    }

    public function verifyCode($code)
    {
        return $this->verification_code === $code && $this->verification_code_status === 'active';
    }


    public function isVerified()
    {
        return !is_null($this->email_verified_at) && $this->verification_code_status === 'inactive';
    }

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

















