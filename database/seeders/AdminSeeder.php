<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@education.az'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('super123'),
                'email_verified_at' => now(),
            ],
        );
        $superAdmin->syncRoles([User::ROLE_SUPER_ADMIN]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@education.az'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles([User::ROLE_ADMIN]);

        $manager = User::updateOrCreate(
            ['email' => 'manager@education.az'],
            [
                'name' => 'Manager',
                'password' => Hash::make('manager123'),
                'email_verified_at' => now(),
            ],
        );
        $manager->syncRoles([User::ROLE_MANAGER]);
    }
}
