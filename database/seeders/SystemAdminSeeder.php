<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemAdminSeeder extends Seeder
{
    public function run()
    {
        $email = env('SYSTEM_ADMIN_EMAIL', 'admin@maaninvite.com');
        $password = env('SYSTEM_ADMIN_PASSWORD', 'ChangeThisPassword123!');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'System Admin',
                'password' => Hash::make($password),
                'role' => 'system_admin',
                'is_system_admin' => true,
                'company_id' => null,
                'organization_id' => null,
            ]
        );
    }
}
