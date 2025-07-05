<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlanSell extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan',
        'price',
        'gateway',
        'status',
        'expire_date',
        'upgrade_data'
    ];

    protected $casts = [
        'upgrade_data' => 'array',
        'expire_date' => 'date'
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get transactions related to this subscription
     */
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    /**
     * Get the N subscription related to this plan sell
     */
    public function nSubscription()
    {
        return $this->hasOne(NSubscription::class, 'subscription_plan_sells_id');
    }

    /**
     * Check if this subscription was upgraded
     */
    public function wasUpgraded()
    {
        return $this->status === 'upgraded';
    }

    /**
     * Get the subscription that this one was upgraded to
     */
    public function upgradedTo()
    {
        if (!$this->wasUpgraded() || !$this->upgrade_data) {
            return null;
        }

        $upgradeData = $this->upgrade_data;
        if (isset($upgradeData['upgraded_to'])) {
            return self::find($upgradeData['upgraded_to']);
        }

        return null;
    }

    /**
     * Scope a query to only include active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expire_date', '>=', now());
    }
}
