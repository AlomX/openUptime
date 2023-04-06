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
        'Monitor_id',
        'response_time',
        'response_code',
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
     * Get the response time in milliseconds.
     */
    public function getResponseTimeMsAttribute(): int
    {
        return $this->response_time * 1000;
    }

    /**
     * Get the response color by code.
     */
    public function getResponseColorAttribute(): string
    {
        switch ($this->response_code) {
            case 200:
                return 'green';
            case 301:
            case 302:
                return 'yellow';
            default:
                return 'red';
        }
    }
}
