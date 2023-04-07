<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ping extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'monitor_id',
        'response_time',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the Monitor that owns the Ping.
     */
    public function Monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    /**
     * Get the response color by code.
     */
    public function getResponseColorAttribute(): string
    {
        // If the response_time is null, the monitor is offline so we return red
        // If the response_time is greater than 500ms, the monitor is slow so we return orange
        // If the response_time is less than 500ms, the monitor is fast so we return green
        if ($this->response_time === null) {
            return 'red';
        } elseif ($this->response_time > 0.5) {
            return 'orange';
        } else {
            return 'green';
        }
    }
}
