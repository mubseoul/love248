<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SendMail extends Model
{
    use HasFactory;

    protected $fillable = [
        'send_email',
        'receiver_email',
        'subject',
        'message',
        'recipient_count',
        'status'
    ];

    protected $casts = [
        'receiver_email' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sender of the email campaign
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'send_email', 'email');
    }

    /**
     * Get formatted recipient count
     */
    public function getFormattedRecipientCountAttribute()
    {
        return number_format($this->recipient_count ?? count($this->receiver_email ?? []));
    }

    /**
     * Get formatted created date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y g:i A');
    }

    /**
     * Get truncated message for display
     */
    public function getTruncatedMessageAttribute()
    {
        return \Str::limit($this->message, 100, '...');
    }

    /**
     * Get recipients as array
     */
    public function getRecipientsArrayAttribute()
    {
        if (is_string($this->receiver_email)) {
            return json_decode($this->receiver_email, true) ?? [];
        }
        return $this->receiver_email ?? [];
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'sent' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            default => 'secondary'
        };
    }
}
