<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        $firstNames = [
            'Aydan', 'Elvin', 'Nərgiz', 'Rauf', 'Səbinə', 'Fərid', 'Leyla', 'Cavid',
            'Aygün', 'Tahir', 'Könül', 'Ruslan', 'Mələk', 'Ayhan', 'Gülnar', 'Samir',
            'Fatimə', 'Kənan', 'Aysel', 'Nicat', 'Pərvin', 'Elçin', 'Zərifə', 'Mətin',
            'Nərmin', 'Ayşən', 'Rəvan', 'Şəfəq',
        ];
        $lastNames = [
            'Nəsirli', 'Məmmədov', 'Məmmədova', 'Həsənli', 'Quliyev', 'Əliyeva',
            'Rzayev', 'Abbasova', 'Cəfərov', 'Hüseynova', 'Kərimli', 'Sadıqov',
            'İbrahimova', 'Vəliyev', 'Bayramova', 'Rəhimli',
        ];

        $last = $this->faker->randomElement($lastNames);
        $first = $this->faker->randomElement($firstNames);

        return [
            'full_name' => $first . ' ' . $last,
            'phone' => '+99450' . $this->faker->numerify('#######'),
            'email' => $this->faker->unique()->safeEmail(),
            'birth_date' => $this->faker->dateTimeBetween('-30 years', '-16 years')->format('Y-m-d'),
            'is_active' => true,
        ];
    }
}
