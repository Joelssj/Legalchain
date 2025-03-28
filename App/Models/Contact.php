<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Contact extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'phone', 'email', 'birthdate', 'folio', 'projects', 'campaigns'];

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_contact', 'contact_id', 'campaign_id')
                ->withTimestamps();
    }
    
}




























// // Contact.php
// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Str;

// class Contact extends Model
// {
//     use HasFactory;

//     protected $fillable = ['id', 'name', 'phone', 'email', 'birthdate'];

//     public $incrementing = false; 
//     protected $keyType = 'string'; 

//     protected static function boot()
//     {
//         parent::boot();

//         static::creating(function ($contact) {
//             $contact->id = (string) Str::uuid();
//         });
//     }

//     /**
//      * Relación muchos a muchos con las etiquetas
//      */
//     public function tags()
//     {
//         return $this->belongsToMany(Tag::class, 'contact_tag');
//     }
// }




// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Str;

// class Contact extends Model
// {
//     use HasFactory;

//     protected $fillable = ['id', 'name', 'phone', 'email', 'birthdate', 'label'];

//     public $incrementing = false; // Desactivar auto-incremento
//     protected $keyType = 'string'; // El UUID es una cadena

//     protected static function boot()
//     {
//         parent::boot();

//         // Generar un UUID automáticamente
//         static::creating(function ($contact) {
//             $contact->id = (string) Str::uuid();
//         });
//     }
// }
