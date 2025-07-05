<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StreamingPrice;
class StreamingTime extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function getBelongStreamerPrice()
    {
        return $this->belongsTo(StreamingPrice::class , 'streamer_time_id' , 'id');
    }
}
