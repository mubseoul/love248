<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamerAvailability extends Model
{
    use HasFactory;

    protected $table = 'streamer_availability';

    protected $fillable = [
        'streamer_id',
        'start_time',
        'end_time',
        'days_of_week',
        'tokens_per_minute'
    ];

    /**
     * Attributes that should be cast
     */
    protected $casts = [];

    /**
     * Get days_of_week attribute - convert string to array if needed
     */
    public function getDaysOfWeekAttribute($value)
    {
        if (is_string($value) && !empty($value)) {
            $result = array_map('intval', explode(',', $value));
            return $result;
        }

        if (is_array($value)) {
            return $value;
        }

        return [];
    }

    /**
     * Set days_of_week attribute - converts array to string if needed
     */
    public function setDaysOfWeekAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['days_of_week'] = implode(',', $value);
        } else if (is_string($value)) {
            $this->attributes['days_of_week'] = $value;
        } else {
            $this->attributes['days_of_week'] = '';
        }
    }

    /**
     * Get the streamer that owns this availability slot
     */
    public function streamer()
    {
        return $this->belongsTo(User::class, 'streamer_id');
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute()
    {
        return $this->start_time . ' - ' . $this->end_time;
    }

    /**
     * Get formatted days
     */
    public function getFormattedDaysAttribute()
    {
        $daysMap = [
            0 => 'Sun',
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat'
        ];

        if (!$this->days_of_week) {
            return '';
        }

        $days = is_array($this->days_of_week) ? $this->days_of_week : explode(',', $this->days_of_week);

        return collect($days)
            ->map(function ($day) use ($daysMap) {
                return $daysMap[$day] ?? '';
            })
            ->filter()
            ->implode(', ');
    }
}
