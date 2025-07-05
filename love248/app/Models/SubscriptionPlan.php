<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;
    protected $fillable = [
        'subscription_name',
        'subscription_price',
        'Is_purchase',
        'days',
        'details',
        'status',
        'subscription_level',
    ];

    // Subscription level constants
    const LEVEL_FREE = 1;
    const LEVEL_PREMIUM = 2;
    const LEVEL_BOOSTED = 3;

    /**
     * Get subscription level name
     */
    public function getLevelNameAttribute()
    {
        return match($this->subscription_level) {
            self::LEVEL_FREE => 'Free',
            self::LEVEL_PREMIUM => 'Premium', 
            self::LEVEL_BOOSTED => 'Boosted',
            default => 'Unknown'
        };
    }

    /**
     * Check if this plan allows access to premium features
     */
    public function allowsPremiumAccess()
    {
        return $this->subscription_level >= self::LEVEL_PREMIUM;
    }

    /**
     * Check if this plan allows access to boosted features
     */
    public function allowsBoostedAccess()
    {
        return $this->subscription_level >= self::LEVEL_BOOSTED;
    }

    /**
     * Get all subscription sales for this plan
     */
    public function subscriptionSales()
    {
        return $this->hasMany(SubscriptionPlanSell::class, 'subscription_plan', 'subscription_name');
    }
}
