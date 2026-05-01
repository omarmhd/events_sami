<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAccessPass extends Model
{
    use HasFactory;
    use BelongsToOrganization;

    protected $fillable = [
        'event_id',
        'organization_id',
        'company_id',
        'passable_type',
        'passable_id',
        'holder_name',
        'holder_email',
        'type',
        'token',
        'is_used',
        'used_at',
        'sent_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function organization()
    {
        return $this->belongsTo(Company::class, 'organization_id');
    }

    public function passable()
    {
        return $this->morphTo();
    }
}
