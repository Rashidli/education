<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Teacher;
use App\Services\SettingsService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    public function definition(): array
    {
        $subjects = [
            'Riyaziyyat', 'Fizika', 'Kimya', 'Biologiya', 'İngilis dili',
            'Alman dili', 'Rus dili', 'İnformatika', 'Tarix', 'Coğrafiya',
        ];
        $levels = ['A1', 'A2', 'B1', 'B2', 'Başlanğıc', 'Orta', 'İrəliləmiş'];

        $teacher = Teacher::inRandomOrder()->first() ?? Teacher::factory()->create();

        return [
            'name' => $this->faker->randomElement($subjects) . ' — ' . $this->faker->randomElement($levels),
            'teacher_id' => $teacher->id,
            'monthly_price' => SettingsService::defaultPriceFor($teacher->type),
            'starts_on' => now()->subMonths(rand(1, 6))->toDateString(),
            'is_active' => true,
        ];
    }
}
