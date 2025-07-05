<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\SubscriptionPlanSell;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Gallery extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $appends = ['canBePlayed', 'isFresh', 'daysUntilExpiry'];
    public $with = ['category'];
    
    protected $dates = ['last_refreshed_at'];

    public function streamer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return '';
                }

                // If the value contains users/ directory, handle the path based on disk
                if (Str::contains($value, 'users/')) {
                    // For local storage, provide direct access route
                    return route('media.gallery.thumbnail', ['gallery' => $this->id]);
                }

                // For other values, return the original
                return $value;
            },
        );
    }

    protected function getVideoUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->video);
    }

    // slug attribute
    public function getSlugAttribute()
    {
        return Str::slug($this->title);
    }

    public function sales()
    {
        return $this->hasMany(GallerySales::class);
    }

    public function category()
    {
        return $this->belongsTo(VideoCategories::class);
    }

    public function getCanBePlayedAttribute()
    {
        // if video is free, allow anyone to view
        // if (Auth::check()) {
        //     $isplan = SubscriptionPlanSell::where('user_id', Auth::id())
        //         ->where('expire_date', '>', Carbon::now())
        //         ->orderBy('id', 'desc')
        //         ->first();
        //     if ($isplan) {
        //         return true;
        //     }
        // }
        if ($this->price === 0) {
            return true;
        }

        // if video owner, allow to view his own vid
        if (auth()->id() == $this->user_id) {
            return true;
        }

        // if it's free for subscribers and current user is one of them
        if (auth()->check() && $this->free_for_subs == "yes" && auth()->user()->hasSubscriptionTo($this->streamer)) {
            return true;
        }

        // if there's a completed order for this gallery
        if (auth()->check()) {
            return $this->sales()
                ->where('gallery_id', $this->id)
                ->where('user_id', auth()->id())
                ->where('status', 'completed')
                ->exists();
        }


        return false;
    }

    /**
     * Scope to get only fresh galleries (updated within 30 days)
     */
    public function scopeFresh($query)
    {
        return $query->where(function ($q) {
            $q->where('last_refreshed_at', '>=', Carbon::now()->subDays(30))
              ->orWhereNull('last_refreshed_at'); // Include null values for backward compatibility
        });
    }

    /**
     * Scope to get expired galleries (not updated in 30+ days)
     */
    public function scopeExpired($query)
    {
        return $query->where('last_refreshed_at', '<', Carbon::now()->subDays(30))
                    ->whereNotNull('last_refreshed_at');
    }

    /**
     * Check if the gallery is fresh (updated within 30 days)
     */
    public function getIsFreshAttribute()
    {
        if (!$this->last_refreshed_at) {
            return true; // Backward compatibility for old records
        }
        return $this->last_refreshed_at->diffInDays(Carbon::now()) <= 30;
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->last_refreshed_at) {
            return 30; // Backward compatibility
        }
        
        $daysSinceRefresh = $this->last_refreshed_at->diffInDays(Carbon::now());
        return max(0, 30 - $daysSinceRefresh);
    }

    /**
     * Refresh the gallery to reset the 30-day timer
     */
    public function refresh()
    {
        $this->update(['last_refreshed_at' => Carbon::now()]);
        return $this;
    }
}
