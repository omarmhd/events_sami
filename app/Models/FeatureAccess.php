<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'feature_code',
        'feature_name',
        'is_enabled',
        'description',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
