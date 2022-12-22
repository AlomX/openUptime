<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class Monitor extends Model
{
    use HasFactory, UUID;

    protected $fillable = ['name', 'address', 'last_call', 'command', 'created_at', 'edited_at'];

    /**
     * Get the pings.
     */
    public function pings()
    {
        return $this->hasMany(Ping::class);
    }
}
