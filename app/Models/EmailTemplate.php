<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    public const TYPE_INVITATION = 'invitation';
    public const TYPE_TICKET = 'ticket';
    public const TYPE_PUBLIC_ACCEPTED = 'public_accepted';
    public const TYPE_PUBLIC_REJECTED = 'public_rejected';

    protected $fillable = [
        'company_id',
        'event_id',
        'template_type',
        'name',
        'subject_template',
        'body_template',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
