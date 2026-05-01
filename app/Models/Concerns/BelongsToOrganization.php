<?php

namespace App\Models\Concerns;

use App\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (app()->runningInConsole()) {
                return;
            }

            /** @var TenantContext $tenant */
            $tenant = app(TenantContext::class);

            if (!$tenant->hasOrganization()) {
                return;
            }

            $model = $builder->getModel();
            $column = $model->getOrganizationColumn();

            $builder->where($model->getTable() . '.' . $column, $tenant->organizationId());
        });

        static::creating(function ($model) {
            /** @var TenantContext $tenant */
            $tenant = app(TenantContext::class);
            if (!$tenant->hasOrganization()) {
                return;
            }

            $column = $model->getOrganizationColumn();

            if (empty($model->{$column})) {
                $model->{$column} = $tenant->organizationId();
            }

            if ($column === 'organization_id' && property_exists($model, 'fillable') && in_array('company_id', $model->getFillable(), true) && empty($model->company_id)) {
                $model->company_id = $tenant->organizationId();
            }
        });
    }

    public function getOrganizationColumn(): string
    {
        if (property_exists($this, 'organizationColumn') && is_string($this->organizationColumn)) {
            return $this->organizationColumn;
        }

        return 'organization_id';
    }
}

