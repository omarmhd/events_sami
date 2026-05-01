<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicEventRegistration extends Model
{
    use HasFactory;
    use BelongsToOrganization;

    protected $fillable = [
        'event_id',
        'registration_form_id',
        'organization_id',
        'company_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_position',
        'guest_nationality',
        'status',
        'notes',
        'reviewed_by',
        'reviewed_at',
        'approval_token',
        'form_payload',
        'guests_count',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'form_payload' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrationForm()
    {
        return $this->belongsTo(RegistrationForm::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function accessPasses()
    {
        return $this->morphMany(EventAccessPass::class, 'passable');
    }
}
