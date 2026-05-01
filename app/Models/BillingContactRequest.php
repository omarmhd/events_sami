<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingContactRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'plan_code',
        'contact_name',
        'contact_email',
        'contact_phone',
        'message',
        'annual_events',
        'average_attendance',
        'status',
        'contacted_at',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
