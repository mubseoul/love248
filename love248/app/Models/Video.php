<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\SubscriptionPlanSell;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'free_for_subs',
        'thumbnail',
        'video',
        'disk',
        'category_id',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public $appends = ['videoUrl', 'slug', 'canBePlayed', 'thumbnailUrl', 'isFresh', 'daysUntilExpiry'];
    public $with = ['category'];
    
    protected $dates = ['last_refreshed_at'];

    public function streamer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Generate a secure signed URL to access the video
     */
    protected function getVideoUrlAttribute()
    {
        if (empty($this->video)) {
            return null;
        }

        // Check if it's using the new storage structure (contains /users/)
        if (Str::contains($this->video, 'users/')) {
            // Use a signed URL that expires in 1 hour for security
            return URL::temporarySignedRoute(
                'video.stream',
                now()->addHour(),
                ['id' => $this->id]
            );
        }

        // Fall back to the old method for backward compatibility
        return asset('storage/' . $this->video);
    }

    /**
     * Generate a URL to access the thumbnail
     */
    protected function getThumbnailUrlAttribute()
    {
        $rawThumbnail = $this->getRawOriginal('thumbnail');

        if (empty($rawThumbnail)) {
            return null;
        }

        // Check if it's using the new storage structure (contains /users/)
        if (Str::contains($rawThumbnail, 'users/')) {
            // Use a regular URL since thumbnails should be viewable by everyone
            return route('video.thumbnail', ['id' => $this->id]);
        }

        // Fall back to the old method for backward compatibility
        return asset('storage/' . $rawThumbnail);
    }

    // Keeping the old thumbnail attribute for backward compatibility
    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $rawThumbnail = $this->getRawOriginal('thumbnail');

                if (empty($rawThumbnail)) {
                    return null;
                }

                if (Str::contains($rawThumbnail, 'users/')) {
                    return route('video.thumbnail', ['id' => $this->id]);
                }

                return asset('storage/' . $rawThumbnail);
            },
        );
    }

    // slug attribute
    public function getSlugAttribute()
    {
        return Str::slug($this->title);
    }

    public function sales()
    {
        return $this->hasMany(VideoSales::class);
    }

    public function category()
    {
        return $this->belongsTo(VideoCategories::class);
    }

    public function getCanBePlayedAttribute()
    {
        // if video owner, allow to view his own vid
        if (auth()->id() == $this->user_id) {
            return true;
        }

        // For all other users, only approved videos are playable
        if ($this->status == 0) {
            return false;
        }

        // if video is free, allow anyone to view
        if ($this->price === 0) {
            return true;
        }

        // if it's free for subscribers and current user is one of them
        if (auth()->check() && $this->free_for_subs == "yes" && auth()->user()->hasSubscriptionTo($this->streamer)) {
            return true;
        }

        // if there's a completed order for this video
        if (auth()->check()) {
            return $this->sales()
                ->where('video_id', $this->id)
                ->where('user_id', auth()->id())
                ->where('status', 'completed')
                ->exists();
        }

        return false;
    }

    /**
     * Scope to get only fresh videos (updated within 30 days)
     */
    public function scopeFresh($query)
    {
        return $query->where(function ($q) {
            $q->where('last_refreshed_at', '>=', Carbon::now()->subDays(30))
              ->orWhereNull('last_refreshed_at'); // Include null values for backward compatibility
        });
    }

    /**
     * Scope to get expired videos (not updated in 30+ days)
     */
    public function scopeExpired($query)
    {
        return $query->where('last_refreshed_at', '<', Carbon::now()->subDays(30))
                    ->whereNotNull('last_refreshed_at');
    }

    /**
     * Check if the video is fresh (updated within 30 days)
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
     * Refresh the video to reset the 30-day timer
     */
    public function refresh()
    {
        $this->update(['last_refreshed_at' => Carbon::now()]);
        return $this;
    }
}
