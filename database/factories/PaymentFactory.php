<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider' => 'Prov. '.$this->faker->word(),
            'provider_id' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'amount' => $this->faker->randomNumber(3),
            'raw_response' => [
                'message' => $this->faker->text()
            ],
            'receipt_number' => $this->faker->uuid(),
            'reservation_id' => Reservation::factory(),
        ];
    }
}
