<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Campaign extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['id', 'name', 'description', 'send_at'];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'campaign_contact', 'campaign_id', 'contact_id')
            ->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'campaign_tag', 'campaign_id', 'tag_id')
            ->withTimestamps();
    }


}











// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Campaign extends Model
// {
//     use HasFactory;

//     protected $table = 'campaigns';
//     protected $keyType = 'string'; // Usa UUID como clave primaria
//     public $incrementing = false; // Desactiva autoincremento

//     protected $fillable = [
//         'id',
//         'name',
//         'description',
//     ];

//     protected $casts = [
//         'created_at' => 'datetime',
//         'updated_at' => 'datetime',
//     ];

//     public function contacts()
//     {
//         return $this->belongsToMany(Contact::class, 'campaign_contact', 'campaign_id', 'contact_id')
//         ->withTimestamps();  
//     }
// }
