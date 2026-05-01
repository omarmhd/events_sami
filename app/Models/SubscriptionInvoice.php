<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_subscription_id',
        'company_id',
        'invoice_number',
        'amount',
        'tax_amount',
        'total_amount',
        'currency',
        'status',
        'issued_at',
        'paid_at',
        'due_at',
        'payload',
    ];

    protected $casts = [
        'amount' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'due_at' => 'datetime',
        'payload' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function subscription()
    {
        return $this->belongsTo(CompanySubscription::class, 'company_subscription_id');
    }
}
