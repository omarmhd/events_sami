<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class BootstrapSystemAdmin extends Command
{
    protected $signature = 'system:bootstrap-admin {--email=} {--password=} {--name=System Admin} {--role=super_admin}';

    protected $description = 'Create or update the primary system admin account';

    public function handle(): int
    {
        $email = $this->option('email') ?: env('SYSTEM_ADMIN_EMAIL', 'admin@maaninvite.com');
        $password = $this->option('password') ?: env('SYSTEM_ADMIN_PASSWORD', 'ChangeThisPassword123!');
        $name = (string) $this->option('name');
        $role = (string) $this->option('role');

        if (!in_array($role, ['system_admin', 'super_admin', 'saas_admin'], true)) {
            $this->error('Invalid role. Allowed: system_admin, super_admin, saas_admin');
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => $role,
                'is_system_admin' => true,
                'company_id' => null,
                'organization_id' => null,
            ]
        );

        $this->info('System admin is ready.');
        $this->line('Email: ' . $user->email);
        $this->line('Role: ' . $user->role);

        return self::SUCCESS;
    }
}

