<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_type',
        'reference_id',
        'reference_type',
        'amount',
        'currency',
        'payment_method',
        'payment_id',
        'status',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Scope to filter by transaction type
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    // Scope to filter by status
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
