<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StreamingTime;

class StreamingPrice extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getStreamerPrice(){
        return $this->hasOne(StreamingTime::class ,'id','streamer_time_id');
    }
}

