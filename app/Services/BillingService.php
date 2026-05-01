<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\SubscriptionInvoice;
use Carbon\Carbon;

class BillingService
{
    public function createAnnualInvoice(Company $company, CompanySubscription $subscription, $amount)
    {
        $tax = round($amount * 0.15, 2);
        $total = round($amount + $tax, 2);

        return SubscriptionInvoice::create([
            'company_subscription_id' => $subscription->id,
            'company_id' => $company->id,
            'invoice_number' => $this->generateInvoiceNumber($company),
            'amount' => $amount,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'currency' => 'SAR',
            'status' => 'paid',
            'issued_at' => Carbon::now(),
            'paid_at' => Carbon::now(),
            'due_at' => Carbon::now(),
            'payload' => [
                'type' => 'annual_subscription',
                'renewal_reminder_at' => Carbon::now()->addDays(351)->toDateTimeString(),
            ],
        ]);
    }

    protected function generateInvoiceNumber(Company $company)
    {
        return 'INV-' . $company->id . '-' . now()->format('YmdHis');
    }
}
