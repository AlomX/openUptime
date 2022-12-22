<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class Ping extends Model
{
    use HasFactory, UUID;

    /**
     * Get the monitor that link to this ping.
     */
    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
