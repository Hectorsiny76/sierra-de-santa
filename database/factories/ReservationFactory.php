<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $start_date = $this->faker->dateTimeBetween('now', '+1 week');
        $end_date = $this->faker->dateTimeBetween($start_date, '+1 week');
        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'refunded'];

        return [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'guest_count' => $this->faker->numberBetween(1, 10),
            'total_price' => $this->faker->randomNumber( 3),
            'status' => $this->faker->randomElement($statuses),
            'room_id' => Room::factory(),
            'user_id' => User::factory(),
            'package_id' => $this->faker->randomElement([Package::factory(), null]),
        ];
    }
}
