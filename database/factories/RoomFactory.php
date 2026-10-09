<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $validNames = [];

        for($x = 1; $x <= 10; $x++) {
            $validNames[] = 'Cuarto '.$this->faker->word.' '.$this->faker->randomNumber(2);
        }

        return [
            'name' => $this->faker->randomElement($validNames),
            'slug' => $this->faker->slug,
            'general_description' => $this->faker->text,
            'price' => $this->faker->randomNumber(3),
            'characteristics' => [
                0 => 'Caracteristica 1 - '.$this->faker->text(30),
                1 => 'Caracteristica 2 - '.$this->faker->text(30),
                2 => 'Caracteristica 3 - '.$this->faker->text(30),
            ],
            'room_type_id' => RoomType::factory(),
        ];
    }
}
