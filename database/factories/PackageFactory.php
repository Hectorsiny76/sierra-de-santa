<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $validNames = ['PacketeRelajes', 'PaqueteVivas', 'PacketeEmociones', 'PaqueteEncantes'];

        return [
            'name' => $this->faker->randomElement($validNames),
            'short_description' => $this->faker->text.'- 1 -',
            'long_description' => $this->faker->text.'- 2 -',
            'price' => $this->faker->randomNumber(3),
            'characteristics' => [
                0 => 'Caracteristica 1 - '.$this->faker->text(30),
                1 => 'Caracteristica 2 - '.$this->faker->text(30),
                2 => 'Caracteristica 3 - '.$this->faker->text(30),
            ],
        ];
    }
}
