<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Models\User;

class PrivateStream extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getUsersInfo()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    // public function 
}
