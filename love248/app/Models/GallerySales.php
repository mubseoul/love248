<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GallerySales extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'gallery_id',
        'streamer_id',
        'user_id', 
        'price',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function streamer()
    {
        return $this->belongsTo(User::class, 'streamer_id');
    }

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }
}
