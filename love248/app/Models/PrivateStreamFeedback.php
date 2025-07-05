<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateStreamFeedback extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'private_stream_feedbacks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'private_stream_request_id',
        'user_id',
        'feedback_type',
        'rating',
        'comment',
        'user_showed_up',
        'streamer_showed_up',
        'technical_issues',
        'technical_issues_description',
        'inappropriate_behavior',
        'inappropriate_behavior_description',
        'overall_experience',
        'would_recommend',
        'additional_data'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'technical_issues' => 'boolean',
        'inappropriate_behavior' => 'boolean',
        'would_recommend' => 'boolean',
        'user_showed_up' => 'boolean',
        'streamer_showed_up' => 'boolean',
        'additional_data' => 'array'
    ];

    /**
     * Get the private stream request that owns the feedback.
     */
    public function privateStreamRequest()
    {
        return $this->belongsTo(PrivateStreamRequest::class);
    }

    /**
     * Get the user who gave the feedback.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for user feedback.
     */
    public function scopeUserFeedback($query)
    {
        return $query->where('feedback_type', 'user');
    }

    /**
     * Scope for streamer feedback.
     */
    public function scopeStreamerFeedback($query)
    {
        return $query->where('feedback_type', 'streamer');
    }

    /**
     * Check if there are any issues reported.
     */
    public function hasIssues()
    {
        return $this->technical_issues || 
               $this->inappropriate_behavior || 
               !$this->user_showed_up || 
               !$this->streamer_showed_up ||
               in_array($this->overall_experience, ['poor', 'terrible']);
    }

    /**
     * Get rating display with stars.
     */
    public function getRatingDisplayAttribute()
    {
        if (!$this->rating) return 'Not rated';
        
        $stars = str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
        return $stars . " ({$this->rating}/5)";
    }

    /**
     * Get overall experience display.
     */
    public function getOverallExperienceDisplayAttribute()
    {
        $experiences = [
            'excellent' => 'Excellent',
            'good' => 'Good',
            'average' => 'Average',
            'poor' => 'Poor',
            'terrible' => 'Terrible'
        ];
        
        return $experiences[$this->overall_experience] ?? 'Not specified';
    }
} 