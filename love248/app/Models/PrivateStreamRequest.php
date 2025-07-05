<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PrivateStreamRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'streamer_id',
        'availability_id',
        'requested_date',
        'requested_time',
        'duration_minutes',
        'room_rental_tokens',
        'streamer_fee',
        'currency',
        'message',
        'payment_method',
        'payment_id',
        'payment_status',
        'status',
        'stream_key',
        'accepted_at',
        'actual_start_time',
        'countdown_started_at',
        'user_joined_at',
        'stream_ended_at',
        'user_joined',
        'actual_duration_minutes',
        'requires_feedback',
        'streamer_feedback_given',
        'user_feedback_given',
        'has_dispute',
        'dispute_created_at',
        'dispute_resolved_by',
        'dispute_resolved_at',
        'completed_at',
        'cancelled_at',
        'admin_cancelled_at',
        'cancellation_reason',
        'interruption_reason',
        'expires_at',
        'released_at',
        'released_by',
        'tokens_awarded',
        'refund_amount',
        'refund_reason',
        'refunded_at',
        'refunded_by',
        'release_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'requested_date' => 'date',
        'room_rental_tokens' => 'decimal:2',
        'streamer_fee' => 'decimal:2',
        'tokens_awarded' => 'decimal:2',
        'user_joined' => 'boolean',
        'requires_feedback' => 'boolean',
        'streamer_feedback_given' => 'boolean',
        'user_feedback_given' => 'boolean',
        'has_dispute' => 'boolean',
        'accepted_at' => 'datetime',
        'actual_start_time' => 'datetime',
        'countdown_started_at' => 'datetime',
        'user_joined_at' => 'datetime',
        'stream_ended_at' => 'datetime',
        'dispute_created_at' => 'datetime',
        'dispute_resolved_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'admin_cancelled_at' => 'datetime',
        'expires_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    /**
     * Get the user that made the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the streamer that received the request.
     */
    public function streamer()
    {
        return $this->belongsTo(User::class, 'streamer_id');
    }

    /**
     * Get the availability slot associated with this request.
     */
    public function availability()
    {
        return $this->belongsTo(StreamerAvailability::class, 'availability_id');
    }

    /**
     * Get the user who resolved the dispute.
     */
    public function disputeResolver()
    {
        return $this->belongsTo(User::class, 'dispute_resolved_by');
    }

    /**
     * Get the admin who processed the refund.
     */
    public function refundProcessor()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    /**
     * Get the admin who released the payment.
     */
    public function paymentReleaser()
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    /**
     * Get all feedback for this stream.
     */
    public function feedbacks()
    {
        return $this->hasMany(PrivateStreamFeedback::class);
    }

    /**
     * Get the user's feedback for this stream.
     */
    public function userFeedback()
    {
        return $this->hasOne(PrivateStreamFeedback::class)->where('feedback_type', 'user');
    }

    /**
     * Get the streamer's feedback for this stream.
     */
    public function streamerFeedback()
    {
        return $this->hasOne(PrivateStreamFeedback::class)->where('feedback_type', 'streamer');
    }

    /**
     * Get all transactions associated with this private stream request.
     */
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    /**
     * Get only the room rental token transactions.
     */
    public function roomRentalTransactions()
    {
        return $this->morphMany(Transaction::class, 'reference')
            ->where('transaction_type', 'room_rental');
    }

    /**
     * Get only the streamer fee payment transactions.
     */
    public function paymentTransactions()
    {
        return $this->morphMany(Transaction::class, 'reference')
            ->where('transaction_type', 'private_stream_fee');
    }

    /**
     * Get the formatted requested date and time.
     *
     * @return string
     */
    public function getRequestedDateTimeAttribute()
    {
        $date = Carbon::parse($this->requested_date)->format('M d, Y');
        $time = Carbon::parse($this->requested_time)->format('H:i');

        return "{$date} at {$time}";
    }

    /**
     * Get the duration display.
     *
     * @return string
     */
    public function getDurationDisplayAttribute()
    {
        return "{$this->duration_minutes} minutes";
    }

    /**
     * Get the formatted streamer fee with currency.
     *
     * @return string
     */
    public function getFormattedStreamerFeeAttribute()
    {
        return "{$this->currency} {$this->streamer_fee}";
    }

    /**
     * Get the formatted room rental tokens.
     *
     * @return string
     */
    public function getFormattedRoomRentalTokensAttribute()
    {
        return "{$this->room_rental_tokens} tokens";
    }

    /**
     * Check if the request is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expires_at && Carbon::now()->gt($this->expires_at);
    }

    /**
     * Check if the request can be accepted.
     *
     * @return bool
     */
    public function canBeAccepted()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Scope a query to only include pending requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include accepted requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope a query to only include completed requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include expired requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Get payment status display text
     * 
     * @return string
     */
    public function getPaymentStatusDisplayAttribute()
    {
        $statusMap = [
            'requires_confirmation' => 'Pending confirmation',
            'confirmed' => 'Payment authorized',
            'captured' => 'Payment completed',
            'cancelled' => 'Payment cancelled',
            'failed' => 'Payment failed'
        ];

        return $statusMap[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    /**
     * Generate a secure random stream key.
     *
     * @return string
     */
    public static function generateSecureStreamKey()
    {
        do {
            // Generate a secure random string: prefix + random string
            $streamKey = 'pstream_' . Str::random(40);
        } while (self::where('stream_key', $streamKey)->exists());

        return $streamKey;
    }

    /**
     * Boot method to generate stream key when creating a new request.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($streamRequest) {
            if (empty($streamRequest->stream_key)) {
                $streamRequest->stream_key = self::generateSecureStreamKey();
            }
        });
    }

    /**
     * Get the secure stream key, generating one if it doesn't exist.
     *
     * @return string
     */
    public function getSecureStreamKey()
    {
        if (empty($this->stream_key)) {
            $this->stream_key = self::generateSecureStreamKey();
            $this->save();
        }

        return $this->stream_key;
    }

    /**
     * Get the total cost (room rental tokens + streamer fee).
     *
     * @return float
     */
    public function getTotalCostAttribute()
    {
        return $this->room_rental_tokens + $this->streamer_fee;
    }

    /**
     * Check if the stream is ready to start (streamers can start anytime for preparation).
     *
     * @return bool
     */
    public function canStreamerStart()
    {
        if ($this->status !== 'accepted') return false;
        
        $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
            $this->requested_date->format('Y-m-d') . ' ' . $this->requested_time);
        
        $scheduledEndTime = $scheduledTime->copy()->addMinutes($this->duration_minutes);
        $now = Carbon::now();
        
        // Check if scheduled end time has passed
        if ($now->gte($scheduledEndTime)) {
            return false;
        }
        
        // Streamers can start anytime before the scheduled end time (for preparation/setup)
        return true;
    }

    /**
     * Check if users can join the stream (at actual scheduled time).
     *
     * @return bool
     */
    public function canUserJoin()
    {
        if ($this->status !== 'accepted' && $this->status !== 'in_progress') return false;
        
        $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
            $this->requested_date->format('Y-m-d') . ' ' . $this->requested_time);
        
        $scheduledEndTime = $scheduledTime->copy()->addMinutes($this->duration_minutes);
        $now = Carbon::now();
        
        // Check if scheduled end time has passed
        if ($now->gte($scheduledEndTime)) {
            return false;
        }
        
        // Users can only join at the actual scheduled time and before end time
        return $now->gte($scheduledTime);
    }

    /**
     * Check if streamer can start the actual stream (only at scheduled time).
     *
     * @return bool
     */
    public function canStartActualStream()
    {
        if ($this->status !== 'accepted' && $this->status !== 'in_progress') return false;
        
        $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
            $this->requested_date->format('Y-m-d') . ' ' . $this->requested_time);
        
        $scheduledEndTime = $scheduledTime->copy()->addMinutes($this->duration_minutes);
        $now = Carbon::now();
        
        // Check if scheduled end time has passed
        if ($now->gte($scheduledEndTime)) {
            return false;
        }
        
        // Streamers can only start actual stream at the scheduled time (not 5 minutes early)
        return $now->gte($scheduledTime);
    }

    /**
     * Check if countdown should start (when streamer can start preparation - anytime).
     *
     * @return bool
     */
    public function shouldStartCountdown()
    {
        return $this->canStreamerStart();
    }

    /**
     * Legacy method for backward compatibility - now uses actual stream start time.
     *
     * @return bool
     */
    public function canStartNow()
    {
        return $this->canStartActualStream();
    }

    /**
     * Check if both parties have given feedback.
     *
     * @return bool
     */
    public function hasBothFeedbacks()
    {
        return $this->streamer_feedback_given && $this->user_feedback_given;
    }

    /**
     * Check if feedback period has expired (24 hours after stream end).
     *
     * @return bool
     */
    public function feedbackPeriodExpired()
    {
        if (!$this->stream_ended_at) return false;
        
        return Carbon::now()->gt($this->stream_ended_at->addHours(24));
    }

    /**
     * Check if there are conflicting feedbacks that need admin resolution.
     *
     * @return bool
     */
    public function hasConflictingFeedback()
    {
        $userFeedback = $this->userFeedback;
        $streamerFeedback = $this->streamerFeedback;
        
        if (!$userFeedback || !$streamerFeedback) return false;
        
        // Check for conflicts in key areas
        $conflicts = [
            $userFeedback->user_showed_up !== $streamerFeedback->user_showed_up,
            $userFeedback->streamer_showed_up !== $streamerFeedback->streamer_showed_up,
            $userFeedback->hasIssues() || $streamerFeedback->hasIssues(),
            abs(($userFeedback->rating ?? 3) - ($streamerFeedback->rating ?? 3)) >= 3
        ];
        
        return collect($conflicts)->contains(true);
    }

    /**
     * Check if payment can be released automatically.
     *
     * @return bool
     */
    public function canReleasePaymentAutomatically()
    {
        if ($this->has_dispute) return false;
        if (!$this->hasBothFeedbacks()) return false;
        if ($this->hasConflictingFeedback()) return false;
        
        return true;
    }

    /**
     * Start the countdown for the stream (streamer preparation phase).
     */
    public function startCountdown()
    {
        $this->update([
            'countdown_started_at' => Carbon::now(),
            'status' => 'in_progress'
        ]);
    }

    /**
     * Get the time until the actual stream starts (for user access).
     *
     * @return int seconds until stream starts for users
     */
    public function getTimeUntilUserCanJoin()
    {
        $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
            $this->requested_date->format('Y-m-d') . ' ' . $this->requested_time);
        
        $secondsUntil = Carbon::now()->diffInSeconds($scheduledTime, false);
        return max(0, $secondsUntil);
    }

    /**
     * Check if we're in the pre-stream preparation period (anytime before scheduled time).
     *
     * @return bool
     */
    public function isInPreparationPeriod()
    {
        return $this->canStreamerStart() && !$this->canUserJoin();
    }

    /**
     * Check if the stream's scheduled time window has completely expired.
     *
     * @return bool
     */
    public function isScheduledTimeExpired()
    {
        $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
            $this->requested_date->format('Y-m-d') . ' ' . $this->requested_time);
        
        $scheduledEndTime = $scheduledTime->copy()->addMinutes($this->duration_minutes);
        
        return Carbon::now()->gte($scheduledEndTime);
    }

    /**
     * Mark that the user has joined the stream.
     */
    public function markUserJoined()
    {
        $this->update([
            'user_joined' => true,
            'user_joined_at' => Carbon::now()
        ]);
    }

    /**
     * Start the actual stream session.
     */
    public function startStream()
    {
        $this->update([
            'actual_start_time' => Carbon::now()
        ]);
    }

    /**
     * End the stream session.
     */
    public function endStream()
    {
        $actualDuration = null;
        if ($this->actual_start_time) {
            $actualDuration = Carbon::now()->diffInMinutes($this->actual_start_time);
        }
        
        $this->update([
            'stream_ended_at' => Carbon::now(),
            'actual_duration_minutes' => $actualDuration,
            'status' => 'awaiting_feedback',
            'requires_feedback' => true
        ]);
    }

    /**
     * Create a dispute for this stream.
     */
    public function createDispute()
    {
        $this->update([
            'has_dispute' => true,
            'dispute_created_at' => Carbon::now(),
            'status' => 'disputed'
        ]);
    }

    /**
     * Resolve the dispute.
     */
    public function resolveDispute($adminId)
    {
        $this->update([
            'dispute_resolved_by' => $adminId,
            'dispute_resolved_at' => Carbon::now(),
            'status' => 'resolved'
        ]);
    }
}
