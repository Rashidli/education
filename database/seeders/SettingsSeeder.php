<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            SettingsService::LOCAL_DEFAULT_PRICE => [
                'value' => 100,
                'label' => 'Yerli müəllim üçün default aylıq qiymət',
                'group' => 'pricing',
            ],
            SettingsService::FOREIGN_DEFAULT_PRICE => [
                'value' => 200,
                'label' => 'Xarici müəllim üçün default aylıq qiymət',
                'group' => 'pricing',
            ],
            SettingsService::LOCAL_DEFAULT_COMMISSION => [
                'value' => 50,
                'label' => 'Yerli müəllim komissiya (%)',
                'group' => 'commission',
            ],
            SettingsService::FOREIGN_DEFAULT_COMMISSION => [
                'value' => 60,
                'label' => 'Xarici müəllim komissiya (%)',
                'group' => 'commission',
            ],
            SettingsService::PAYMENT_CYCLE_DAYS => [
                'value' => 30,
                'label' => 'Ödəniş dövriyyəsi (gün)',
                'group' => 'payment',
            ],
            SettingsService::UPCOMING_WINDOW_DAYS => [
                'value' => 5,
                'label' => 'Yaxınlaşan ödəniş pəncərəsi (gün)',
                'group' => 'payment',
            ],
        ];

        foreach ($defaults as $key => $meta) {
            Setting::updateOrCreate(
                ['key' => $key],
                array_merge(['key' => $key], $meta),
            );
        }
    }
}
