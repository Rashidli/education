<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Services\SettingsService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    public function definition(): array
    {
        $localNames = [
            'Elmar Hüseynov', 'Vüsal Əliyev', 'Nigar Məmmədova', 'Rəşad Quliyev',
            'Günay Həsənova', 'Tural Abbasov', 'Aynur Cəfərova', 'Orxan Rzayev',
        ];
        $foreignNames = [
            'John Anderson', 'Sarah Williams', 'Michael Chen', 'Emma Taylor',
            'David Schmidt', 'Maria Rodriguez',
        ];

        $type = $this->faker->randomElement([Teacher::TYPE_LOCAL, Teacher::TYPE_FOREIGN]);
        $names = $type === Teacher::TYPE_LOCAL ? $localNames : $foreignNames;

        return [
            'name' => $this->faker->randomElement($names),
            'type' => $type,
            'commission_rate' => SettingsService::defaultCommissionFor($type),
            'phone' => '+99455' . $this->faker->numerify('#######'),
            'email' => $this->faker->unique()->safeEmail(),
            'is_active' => true,
        ];
    }

    public function local(): static
    {
        return $this->state(fn () => [
            'type' => Teacher::TYPE_LOCAL,
            'commission_rate' => SettingsService::defaultCommissionFor(Teacher::TYPE_LOCAL),
        ]);
    }

    public function foreign(): static
    {
        return $this->state(fn () => [
            'type' => Teacher::TYPE_FOREIGN,
            'commission_rate' => SettingsService::defaultCommissionFor(Teacher::TYPE_FOREIGN),
        ]);
    }
}
