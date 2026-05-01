<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationQr extends Model
{
    use HasFactory;

    protected $table = 'invitation_qrs';
    protected $guarded = [''];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function invitation()
    {
        return $this->belongsTo(EventInvitation::class, 'event_invitation_id');
    }

}
