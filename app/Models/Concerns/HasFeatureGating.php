<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasFeatureGating
{
    public function scopeWithFeatureAccess(Builder $query, string $featureCode): Builder
    {
        return $query->whereHas('company', function ($q) use ($featureCode) {
            $q->whereHas('featureAccess', function ($q) use ($featureCode) {
                $q->where('feature_code', $featureCode)
                    ->where('is_enabled', true);
            });
        });
    }

    public function canAccessFeature(string $featureCode): bool
    {
        return $this->company
            ->featureAccess()
            ->where('feature_code', $featureCode)
            ->where('is_enabled', true)
            ->exists();
    }
}
