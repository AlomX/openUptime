<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Monitor extends Model
{
    use HasFactory, HasUuids;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->order = Monitor::max('order') + 1;
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'interval',
        'command',
        'url',
        'note',
        'icon',
        'key',
        'user_id',
        'links',
        'order',
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
     * Get the user that owns the Monitor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the Pings for the Monitor.
     */
    public function Pings(): HasMany
    {
        return $this->hasMany(Ping::class);
    }

    /**
     * Get the 50 latest Ping for the Monitor.
     */
    public function latestPings(): HasMany
    {
        if($this->interval <= 60000) {
            $count = 50;
        }else if($this->interval <= 300000) {
            $count = 24;
        }else if($this->interval <= 1800000) {
            $count = 12;
        }else {
            $count = 6;
        }
        return $this->hasMany(Ping::class)->latest()->limit($count);
    }

    /**
     * Get the last Ping for the Monitor.
     */
    public function lastPing(): ?Ping
    {
        return $this->hasMany(Ping::class)->latest()->first();
    }
}
