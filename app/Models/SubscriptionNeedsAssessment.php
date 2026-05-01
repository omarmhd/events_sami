<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionNeedsAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'annual_events',
        'average_attendance',
        'needs_customization',
        'recommended_plan_code',
        'answered_at',
    ];

    protected $casts = [
        'needs_customization' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
