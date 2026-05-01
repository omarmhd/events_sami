<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'company_id',
        'event_id',
        'source_type',
        'source_id',
        'holder_name',
        'holder_email',
        'guest_count',
        'token',
        'status',
        'checked_in_at',
        'checked_in_count',
        'qr_payload',
    ];

    protected $casts = [
        'guest_count' => 'integer',
        'checked_in_count' => 'integer',
        'checked_in_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Company::class, 'organization_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function checkinLogs()
    {
        return $this->hasMany(TicketCheckinLog::class);
    }
}

