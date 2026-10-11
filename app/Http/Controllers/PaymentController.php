<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\ReservationStatus;
use App\Services\Payments\PaymentGatewayInterface;

class PaymentController extends Controller
{
    public function __construct(protected PaymentGatewayInterface $gateway){}

    public function checkout(User $user, Reservation $reservation){

        $reservation = $user->reservations()->findOrFail($reservation->id);
        $this->authorize('view', $reservation);

        if($reservation->status != ReservationStatus::Pending){
            return response ()->json(["message" => "Esta reservación no puede ser pagada."], 409);
        }

        $url = $this->gateway->createCheckoutSession($reservation);

        return response ()->json(["checkout_url" => $url]);

    }
}
