<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code_hash',
        'attempts',
        'expires_at',
        'consumed_at',
        'created_ip',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public function isExpired()
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isConsumed()
    {
        return $this->consumed_at !== null;
    }
}
