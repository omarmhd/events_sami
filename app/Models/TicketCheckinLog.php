<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCheckinLog extends Model
{
    use HasFactory;
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'event_id',
        'ticket_id',
        'validated_by',
        'result',
        'scanned_token',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}

