<?php

namespace App\Tenancy;

use App\Models\Company;

class TenantContext
{
    public const MODE_SYSTEM = 'system';
    public const MODE_ORGANIZER = 'organizer';
    public const MODE_UNKNOWN = 'unknown';

    protected ?Company $organization = null;
    protected string $mode = self::MODE_UNKNOWN;
    protected ?string $host = null;
    protected ?string $subdomain = null;

    public function setSystem(string $host): void
    {
        $this->mode = self::MODE_SYSTEM;
        $this->host = $host;
        $this->organization = null;
        $this->subdomain = null;
    }

    public function setOrganization(Company $organization, string $host, string $subdomain): void
    {
        $this->mode = self::MODE_ORGANIZER;
        $this->organization = $organization;
        $this->host = $host;
        $this->subdomain = $subdomain;
    }

    public function clear(string $host): void
    {
        $this->mode = self::MODE_UNKNOWN;
        $this->host = $host;
        $this->organization = null;
        $this->subdomain = null;
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function host(): ?string
    {
        return $this->host;
    }

    public function subdomain(): ?string
    {
        return $this->subdomain;
    }

    public function organization(): ?Company
    {
        return $this->organization;
    }

    public function organizationId(): ?int
    {
        return $this->organization?->id;
    }

    public function isSystem(): bool
    {
        return $this->mode === self::MODE_SYSTEM;
    }

    public function hasOrganization(): bool
    {
        return $this->organization !== null;
    }
}

