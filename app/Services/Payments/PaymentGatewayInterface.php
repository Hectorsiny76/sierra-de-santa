<?php

namespace App\Services\Payments;

use App\Models\Reservation;

interface PaymentGatewayInterface
{
    public function createCheckoutSession(Reservation $reservation) : string;

    public function verifyWebhook(string $payload, string $signature): array;
}
