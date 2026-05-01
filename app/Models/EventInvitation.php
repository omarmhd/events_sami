<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventInvitation extends Model
{
    use HasFactory;
    use BelongsToOrganization;

    protected $fillable = [
        'event_id',
        'organization_id',
        'company_id',
        'invitee_name',
        'invitee_email',
        'invitee_phone',
        'invitee_position',
        'invitee_nationality',
        'status',
        'allowed_guests',
        'selected_guests',
        'responded_at',
        'invitation_token',
        'flow_type',
        'source',
        'last_sent_at',
        'response_token',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    public function InvitationQrs()
    {
        return $this->hasMany(InvitationQr::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function accessPasses()
    {
        return $this->morphMany(EventAccessPass::class, 'passable');
    }

}
