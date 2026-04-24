<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public const PERMISSIONS = [
        'dashboard.view' => 'Dashboard-a bax',

        'students.view' => 'Tələbələrə bax',
        'students.manage' => 'Tələbələri idarə et',

        'groups.view' => 'Qruplara bax',
        'groups.manage' => 'Qrupları idarə et',

        'teachers.view' => 'Müəllimlərə bax',
        'teachers.manage' => 'Müəllimləri idarə et',

        'enrollments.manage' => 'Qrupa qoşma/çıxarma',

        'payments.view' => 'Ödənişlərə bax',
        'payments.create' => 'Ödəniş qeydə al',
        'payments.delete' => 'Ödənişi sil',

        'payouts.view' => 'Müəllim ödənişlərinə bax',
        'payouts.create' => 'Müəllimə ödəniş et',
        'payouts.delete' => 'Müəllim ödənişini sil',

        'reports.view' => 'Hesabatlara bax',
        'reports.export' => 'Hesabat ixrac et',

        'settings.manage' => 'Parametrləri idarə et',

        'users.manage' => 'İstifadəçi və rolları idarə et',
    ];

    public function run(): void
    {
        Artisan::call('permission:cache-reset');

        foreach (self::PERMISSIONS as $name => $label) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => User::ROLE_SUPER_ADMIN, 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => User::ROLE_ADMIN, 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => User::ROLE_MANAGER, 'guard_name' => 'web']);

        $superAdmin->syncPermissions(array_keys(self::PERMISSIONS));

        $admin->syncPermissions([
            'dashboard.view',
            'students.view', 'students.manage',
            'groups.view', 'groups.manage',
            'teachers.view', 'teachers.manage',
            'enrollments.manage',
            'payments.view', 'payments.create', 'payments.delete',
            'payouts.view', 'payouts.create', 'payouts.delete',
            'reports.view', 'reports.export',
        ]);

        $manager->syncPermissions([
            'dashboard.view',
            'students.view',
            'groups.view',
            'teachers.view',
            'payments.view', 'payments.create',
            'payouts.view',
            'reports.view',
        ]);
    }
}
