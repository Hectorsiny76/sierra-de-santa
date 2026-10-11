<?php

namespace App\Services\Payments;

use App\Models\Reservation;
use App\Services\Payments\PaymentGatewayInterface;

class FakePaymentGateway implements PaymentGatewayInterface
{

    public function createCheckoutSession(Reservation $reservation): string
    {
        return route('payments.fake-checkout', ['reservation_id' => $reservation->id]);
    }

    public function verifyWebhook(string $payload, string $signature): array
    {
        return json_decode($payload, true);
    }
}
