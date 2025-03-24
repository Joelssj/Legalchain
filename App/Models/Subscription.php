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









//con fecha de terminacion
// // app/Models/Subscription.php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Str;

// class Subscription extends Model
// {
//     use HasFactory;

//     // Establecer que el ID de la suscripción es de tipo string
//     protected $primaryKey = 'id';  // Usamos 'id' como clave primaria
//     public $incrementing = false;  // Desactivar autoincremento
//     protected $keyType = 'string'; // Definir tipo string para el ID (UUID)

//     protected $fillable = [
//         'user_id',  // ID del usuario relacionado
//         'plan',     // Plan de suscripción
//         'start_date', // Fecha de inicio
//         'end_date',   // Fecha de fin
//     ];

//     // Hook para generar el UUID al crear la suscripción
//     protected static function boot()
//     {
//         parent::boot();

//         static::creating(function ($subscription) {
//             if (empty($subscription->id)) {
//                 $subscription->id = (string) Str::uuid();  // Generar UUID
//             }
//         });
//     }
// }
