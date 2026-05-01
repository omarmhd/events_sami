<?php

namespace App\Models\Concerns;

use Carbon\Carbon;

trait HasTrialPeriod
{
    public function isOnTrial(): bool
    {
        return $this->status === 'trial' &&
            $this->trial_ends_at &&
            Carbon::now()->isBefore($this->trial_ends_at);
    }

    public function getRemainingTrialDays(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }

        return max(0, $this->trial_ends_at->diffInDays(Carbon::now(), false));
    }

    public function getTrialProgressPercentage(): int
    {
        if (!$this->trial_started_at || !$this->trial_ends_at) {
            return 0;
        }

        $totalDays = $this->trial_started_at->diffInDays($this->trial_ends_at);
        $elapsedDays = $this->trial_started_at->diffInDays(Carbon::now());

        return min(100, (int) (($elapsedDays / $totalDays) * 100));
    }
}
