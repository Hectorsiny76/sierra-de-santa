<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\PaymentStatus;
use App\ReservationStatus;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(protected PaymentGatewayInterface $gateway){}

    public function checkout(User $user, Reservation $reservation){
        $reservation = $user->reservations()->findOrFail($reservation->id);
        $this->authorize('view', $reservation);

        if($reservation->status != ReservationStatus::Pending){

            Log::error('Intento de reservación fallido.', [
                'reservation_id' => $reservation->id,
                'status' => $reservation->status,
                'user_id' => $user->id,
            ]);

            return response ()->json(["message" => "Esta reservación no puede ser pagada."], 409);
        }

        $url = $this->gateway->createCheckoutSession($reservation);

        return response ()->json(["checkout_url" => $url]);
    }

    public function fakePay(Request $request){

        $payload = json_encode([
            'reservation_id' => $request->reservation,
            'status' => 'succeeded',
            'provider_id' => 'fake_'.uniqid(),
        ]);

        $event = $this->gateway->verifyWebhook($payload, 'fake-signature');

        Log::info('', [
            'payload' => $payload,
            'IP' => $request->ip(),
            'event' => $event,
        ]);

        return $this->handlePaymentEvent($event);
    }

    public function webhook(Request $request){
        $event = $this->gateway->verifyWebhook($request->getContent(), $request->header('Stripe-Signature'));

        return $this->handlePaymentEvent($event);
    }

    public function handlePaymentEvent($event): JsonResponse{

        $reservation = Reservation::findOrFail($event['reservation_id']);

        Payment::create([
            'reservation_id' => $reservation->id,
            'provider' => 'fake',
            'provider_id' => $event['provider_id'],
            'status' => PaymentStatus::Succeeded,
            'amount' => $reservation->total_price,
            'raw_response' => $event,
            'receipt_number' => 'fake_receipt_number'.uniqid(),
        ]);

        $reservation->update(['status' => ReservationStatus::Confirmed]);

        return response()->json(['message' => 'Pago procesado, Reservación confirmada.']);
    }
}
