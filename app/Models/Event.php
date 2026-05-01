<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'company_id',
        'created_by',
        'event_slug',
        'slug',
        'name',
        'title',
        'event_type',
        'experience_type',
        'registration_mode',
        'registration_form_id',
        'date',
        'from_time',
        'to_time',
        'location_name',
        'google_map_url',
        'description',
        'description_en',
        'start_datetime',
        'end_datetime',
        'capacity',
        'schedule_items',
        'header_image_path',
        'footer_image_path',
        'requires_manual_approval',
        'allow_reentry',
        'dynamic_form_schema',
        'rejection_email_enabled',
        'invitation_email_subject',
        'invitation_email_body',
        'confirmation_email_subject',
        'confirmation_email_body',
        'status',
        'published_at',
    ];

    protected $casts = [
        'date' => 'date',
        'published_at' => 'datetime',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'schedule_items' => 'array',
        'requires_manual_approval' => 'boolean',
        'allow_reentry' => 'boolean',
        'dynamic_form_schema' => 'array',
        'rejection_email_enabled' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrationForm()
    {
        return $this->belongsTo(RegistrationForm::class);
    }

    public function invitations()
    {
        return $this->hasMany(EventInvitation::class);
    }

    public function publicRegistrations()
    {
        return $this->hasMany(PublicEventRegistration::class);
    }

    public function accessPasses()
    {
        return $this->hasMany(EventAccessPass::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function checkinLogs()
    {
        return $this->hasMany(TicketCheckinLog::class);
    }

    public function emailTemplates()
    {
        return $this->hasMany(EmailTemplate::class);
    }
}
