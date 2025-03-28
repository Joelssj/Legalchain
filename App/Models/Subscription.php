<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan',
        'start_date',
        'end_date',
    ];

    // Asegúrate de que 'id' sea un UUID
    public $incrementing = false;
    protected $keyType = 'string';  // Cambiar tipo de clave a 'string' porque estamos usando UUID

    // Hook para generar el UUID al crear una nueva suscripción
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();  // Generar un UUID automáticamente si no tiene un 'id'
            }
        });
    }
}