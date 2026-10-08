<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomImage>
 */
class RoomImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => '/storage/room_images/'.$this->faker->word.'jpg',
            'name' => $this->faker->word(),
            'description' => $this->faker->text,
            'room_id' => Room::factory(),
        ];
    }
}
