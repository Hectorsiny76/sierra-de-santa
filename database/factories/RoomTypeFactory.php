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
        return [
            'name' => 'Cuarto Tipo '.$this->faker->word.' '.$this->faker->randomNumber(2),
            'img_path' => '/storage/room_type/'.$this->faker->word.'.jpg',
            'description' => $this->faker->text,
            'max_capacity' => $this->faker->randomNumber(1),
        ];
    }
}
