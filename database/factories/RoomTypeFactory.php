<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomType>
 */
class RoomTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $validNames = ['Individual', 'Pareja', 'Familiar', 'Extra Familiar'];

        return [
            'name' => $this->faker->randomElement($validNames),
            'path' => '/storage/room_type/'.$this->faker->word.'.jpg',
            'description' => $this->faker->text,
        ];
    }
}
