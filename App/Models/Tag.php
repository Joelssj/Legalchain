<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['contact_id', 'name'];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

        // Relación muchos a muchos con Campaigns
    public function campaigns()
    {
    return $this->belongsToMany(Campaign::class, 'campaign_tag', 'tag_id', 'campaign_id')->withTimestamps();
    }
    
}

