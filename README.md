# MaanInvite

Multi-tenant SaaS for event invitations and QR ticketing.

## Stack

- PHP 8.3+
- Laravel 11 target architecture (current codebase compatible with existing app runtime)
- MySQL 8
- Livewire 3 + Filament v3 (panel providers scaffolded)
- Queue: database driver (Redis-ready)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

## Required .env Variables

```env
APP_NAME=MaanInvite
APP_URL=http://maaninvite.test:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maaninvite
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@maaninvite.com"
MAIL_FROM_NAME="${APP_NAME}"

TENANCY_BASE_DOMAIN=maaninvite.test
TENANCY_ADMIN_SUBDOMAIN=admin
TENANCY_ORGANIZER_ADMIN_PATH=admin
TENANCY_ALLOW_UNKNOWN_HOSTS=true

SYSTEM_ADMIN_EMAIL=admin@maaninvite.com
SYSTEM_ADMIN_PASSWORD=ChangeMe123!
```

## Local Hosts File

Add these entries to your hosts file:

```txt
127.0.0.1 maaninvite.test
127.0.0.1 admin.maaninvite.test
127.0.0.1 acme.maaninvite.test
```

Use any organization subdomain instead of `acme` after creating organization records.

## Run App + Queue Worker

```bash
php artisan serve --host=0.0.0.0 --port=8000
php artisan queue:work
```

## Bootstrap System Admin (Important)

```bash
php artisan system:bootstrap-admin
```

Custom values:

```bash
php artisan system:bootstrap-admin --email=admin@maaninvite.com --password='StrongPass123!' --role=super_admin
```

## Main URLs

- System admin domain: `http://admin.maaninvite.test:8000/admin`
- System admin login page: `http://admin.maaninvite.test:8000/admin/login`
- Organizer domain panel: `http://{subdomain}.maaninvite.test:8000/admin`
- OTP onboarding: `http://{subdomain}.maaninvite.test:8000/onboarding/login`
- Public event page: `http://{subdomain}.maaninvite.test:8000/events/{slug}`
- Private invite page: `http://{subdomain}.maaninvite.test:8000/invites/{token}`
- Ticket page: `http://{subdomain}.maaninvite.test:8000/tickets/{token}`

## Seeded Access

After `php artisan migrate --seed`:

- System admin email/password come from `SYSTEM_ADMIN_EMAIL` / `SYSTEM_ADMIN_PASSWORD`.

## Useful Commands

```bash
php artisan migrate:fresh --seed
php artisan route:list
php artisan queue:work --tries=3
```

## Implemented Foundation in This Iteration

- Tenant resolution middleware by host/subdomain.
- Tenant context singleton (`TenantContext`).
- Organization-scoped model trait (`BelongsToOrganization`).
- Schema alignment migration for `organization_id` + event/ticket/check-in fields.
- Ticket check-in endpoint + check-in logs.
- OTP flow refactor with FormRequests + Actions + queued OTP mailable.
- Filament panel providers scaffold:
  - System panel (`admin.{base_domain}`)
  - Organizer panel (`{subdomain}.{base_domain}/admin`)
