<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SubscriptionPlanSell;

class NSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subscription_plan_sells_id',
        'status',
        'user_id',
        'subscription_status',
        'subscription_id',
        'expired_at'
    ];

    protected $casts = [
        'expired_at' => 'datetime'
    ];

    public function subscriptionPlanSell()
    {
        return $this->belongsTo(SubscriptionPlanSell::class, 'subscription_plan_sells_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
