<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Overtrue\LaravelFollow\Traits\Follower;
use Overtrue\LaravelFollow\Traits\Followable;
use App\Models\getUsersInfo;
use App\Models\Commission;
use App\Models\Video;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Followable;
    use Notifiable;
    use Follower;
    use HasRoles;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $appends = ['firstCategory', 'moneyBalance', 'isBanned', 'firstName', 'creditBalance'];

    /**
     * Check if the user is an admin
     * 
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin === 'yes';
    }

    protected function profilePicture(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_null($value) ? Storage::disk('public')->url('images/default-profile-pic.png') : Storage::disk('public')->url($value),
        );
    }

    public function getFirstNameAttribute()
    {
        $fullname = explode(" ", $this->name);
        return isset($fullname[0]) ? $fullname[0] : __('Me');
    }


    protected function coverPicture(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_null($value) ? Storage::disk('public')->url('images/default-cover-pic.png') : Storage::disk('public')->url($value),
        );
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    protected function getFirstCategoryAttribute()
    {
        return $this->categories()->firstOr(function () {
            return (object)['id' => null, 'category' => null, 'slug' => null];
        });
    }

    public function scopeIsStreamer($query)
    {
        return $query->where('is_streamer', 'yes')->where('is_streamer_verified', 'yes');
    }

    public function getIsBannedAttribute()
    {
        return Banned::where('ip', $this->ip)->exists();
    }

    protected function getMoneyBalanceAttribute()
    {
        if (is_int($this->tokens)) {
            return opt('token_value') * $this->tokens;
        }

        return;
    }

    protected function getCreditBalanceAttribute()
    {
        return $this->credit_balance ?? 0;
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function tiers()
    {
        return $this->hasMany(Tier::class);
    }

    public function hasSubscriptionTo(User $streamer)
    {
        return $this->subscriptions()->where('subscription_expires', '>=', now())
            ->where('streamer_id', $streamer->id)
            ->exists();
    }

    public function streamerBans()
    {
        return $this->hasMany(RoomBans::class, 'streamer_id');
    }

    public function bannedFromRooms()
    {
        return $this->hasMany(RoomBans::class, 'user_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'subscriber_id', 'id');
    }

    public function subscribers()
    {
        return $this->hasMany(Subscription::class, 'streamer_id', 'id')->where('subscription_expires', '>=', now());
    }

    public function videos()
    {
        return $this->hasMany(Video::class)->where('status', 1);
    }

    public function gallery()
    {
        return $this->hasMany(Gallery::class);
    }

    public function purchasedVideos()
    {
        return $this->hasManyThrough(Video::class, VideoSales::class, 'user_id', 'id', 'id', 'video_id')
            ->where('video_sales.status', 'completed');
    }
    public function purchasedGallery()
    {
        return $this->hasManyThrough(Gallery::class, GallerySales::class, 'user_id', 'id', 'id', 'gallery_id')
            ->where('gallery_sales.status', 'completed');
    }
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function tipsGiven()
    {
        return $this->hasMany(Tips::class, 'user_id', 'id');
    }

    public function tipsReceived()
    {
        return $this->hasMany(Tips::class, 'streamer_id', 'id');
    }

    public function tokenOrders()
    {
        return $this->hasMany(TokenSale::class);
    }

    public function privateList()
    {
        return $this->belongsTo(PrivateStream::class, 'user_id', 'id');
    }

    public function getPrivateStremEarning()
    {
        return $this->hasMany(PrivateStream::class, 'streamer_id', 'id')->where('status', 'conform');
    }
    public function getVideoSales()
    {
        return $this->hasMany(VideoSales::class, 'streamer_id', 'id');
    }
    public function getGallerySales()
    {
        return $this->hasMany(GallerySales::class, 'streamer_id', 'id');
    }
    public function getCommission()
    {
        return $this->hasMany(Commission::class, 'streamer_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            DB::transaction(function () use ($user) {
                $user->purchasedVideos()->delete();
                $user->videos()->delete();
                $user->gallery()->delete();
                $user->purchasedGallery()->delete();
                $user->subscriptions()->delete();
                $user->tiers()->delete();
                $user->categories()->delete();
                $user->notifications()->delete();
                $user->chats()->delete();
                $user->tipsGiven()->delete();
                $user->tipsReceived()->delete();
                $user->withdrawals()->delete();

                DB::statement('DELETE FROM followables WHERE user_id = ? OR followable_id = ?', [$user->id, $user->id]);
            }, 3);
        });
    }

    public function latestVideo()
    {
        return $this->hasOne(Video::class)->latestOfMany('created_at');
    }

    /**
     * Get user's subscription plan sales (platform subscriptions)
     */
    public function subscriptionPlanSales()
    {
        return $this->hasMany(SubscriptionPlanSell::class);
    }

    /**
     * Get user's current active subscription plan
     */
    public function getActiveSubscriptionPlan()
    {
        $activeSale = $this->subscriptionPlanSales()
            ->where('status', 'active')
            ->where('expire_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$activeSale) {
            return null;
        }

        return SubscriptionPlan::where('subscription_name', $activeSale->subscription_plan)->first();
    }

    /**
     * Get user's current subscription level (1=Free, 2=Premium, 3=Boosted)
     */
    public function getSubscriptionLevel()
    {
        $activePlan = $this->getActiveSubscriptionPlan();
        return $activePlan ? $activePlan->subscription_level : SubscriptionPlan::LEVEL_FREE;
    }

    /**
     * Check if user has premium access (Level 2+)
     */
    public function hasPremiumAccess()
    {
        return $this->getSubscriptionLevel() >= SubscriptionPlan::LEVEL_PREMIUM;
    }

    /**
     * Check if user has boosted access (Level 3)
     */
    public function hasBoostedAccess()
    {
        return $this->getSubscriptionLevel() >= SubscriptionPlan::LEVEL_BOOSTED;
    }

    /**
     * Check if user can access private rooms
     */
    public function canAccessPrivateRooms()
    {
        return $this->hasPremiumAccess();
    }

    /**
     * Check if user can access media gallery
     */
    public function canAccessMediaGallery()
    {
        return $this->hasPremiumAccess();
    }

    /**
     * Check if user can make proposals (private stream requests)
     */
    public function canMakeProposals()
    {
        return $this->hasPremiumAccess();
    }

    /**
     * Check if user has profile highlighting
     */
    public function hasProfileHighlighting()
    {
        return $this->hasBoostedAccess();
    }

    /**
     * Check if user has search priority
     */
    public function hasSearchPriority()
    {
        return $this->hasBoostedAccess();
    }

    /**
     * Get subscription level display name
     */
    public function getSubscriptionLevelName()
    {
        return match($this->getSubscriptionLevel()) {
            SubscriptionPlan::LEVEL_FREE => 'Free',
            SubscriptionPlan::LEVEL_PREMIUM => 'Premium',
            SubscriptionPlan::LEVEL_BOOSTED => 'Boosted',
            default => 'Free'
        };
    }
}
